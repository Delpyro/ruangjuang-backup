<?php

namespace App\Livewire\Admin\Tryouts;

use App\Models\Tryout;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class TryoutsManage extends Component
{
    use WithPagination, WithFileUploads;

    public $title, $slug, $is_hots = false, $duration, $content, $quote, $price, $discount, $is_active = true;
    public $tryoutId;
    public $isEdit = false;
    public $showModal = false;
    public $confirmingDeletion = false;
    public $tryoutToDelete;
    public $showTrashed = false;

    // Untuk search
    public $search = '';

    protected $queryString = ['search', 'showTrashed'];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tryouts,slug,' . $this->tryoutId,
            'is_hots' => 'boolean',
            'duration' => 'nullable|integer|min:1',
            'content' => 'required|string',
            'quote' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|max:' . $this->price, // Diskon maksimal sama dengan harga
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTitle($value)
    {
        if (!$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    // Validasi diskon saat harga berubah
    public function updatedPrice($value)
    {
        if ($this->discount > $value) {
            $this->discount = $value;
        }
    }

    public function render()
    {
        $tryouts = Tryout::when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed();
            }, function ($query) {
                $query->whereNull('deleted_at');
            })
            // PERUBAHAN: Menggunakan oldest() agar data terlama/tertua menjadi Nomor 1 (di urutan teratas).
            ->oldest() 
            ->paginate(10);

        return view('livewire.admin.tryouts.tryouts-manage', [
            'tryouts' => $tryouts,
        ])->layout('layouts.admin');
    }

    public function openModal($edit = false, $id = null)
    {
        $this->resetForm();
        $this->isEdit = $edit;

        if ($edit && $id) {
            $this->edit($id);
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function resetForm()
    {
        $this->reset(['title', 'slug', 'is_hots', 'duration', 'content', 'quote', 'price', 'discount', 'is_active', 'tryoutId', 'isEdit']);
        $this->is_hots = false;
        $this->is_active = true;
    }

    public function create()
    {
        $this->validate();

        Tryout::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'is_hots' => $this->is_hots,
            'duration' => $this->duration,
            'content' => $this->content,
            'quote' => $this->quote,
            'price' => $this->price,
            'discount' => $this->discount, // Sekarang dalam bentuk nominal
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'Tryout berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tryout = Tryout::withTrashed()->findOrFail($id);
        $this->tryoutId = $id;
        $this->title = $tryout->title;
        $this->slug = $tryout->slug;
        $this->is_hots = $tryout->is_hots;
        $this->duration = $tryout->duration;
        $this->content = $tryout->content;
        $this->quote = $tryout->quote;
        $this->price = $tryout->price;
        $this->discount = $tryout->discount; // Sekarang dalam bentuk nominal
        $this->is_active = $tryout->is_active;
    }

    public function update()
    {
        $this->validate();

        $tryout = Tryout::withTrashed()->findOrFail($this->tryoutId);
        
        $tryout->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'is_hots' => $this->is_hots,
            'duration' => $this->duration,
            'content' => $this->content,
            'quote' => $this->quote,
            'price' => $this->price,
            'discount' => $this->discount, // Sekarang dalam bentuk nominal
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'Tryout berhasil diperbarui.');
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->tryoutToDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->tryoutToDelete = null;
    }

    public function delete()
    {
        try {
            $tryout = Tryout::findOrFail($this->tryoutToDelete);
            $tryout->delete();
            
            $this->confirmingDeletion = false;
            $this->tryoutToDelete = null;
            session()->flash('success', 'Tryout berhasil dihapus (soft delete).');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus tryout: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $tryout = Tryout::withTrashed()->findOrFail($id);
            $tryout->forceDelete();
            session()->flash('success', 'Tryout berhasil dihapus permanen.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            Tryout::withTrashed()->findOrFail($id)->restore();
            session()->flash('success', 'Tryout berhasil direstore.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal merestore tryout: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $tryout = Tryout::findOrFail($id);
            $tryout->update(['is_active' => !$tryout->is_active]);
            
            session()->flash('success', 'Status tryout berhasil diubah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function toggleHots($id)
    {
        try {
            $tryout = Tryout::findOrFail($id);
            $tryout->update(['is_hots' => !$tryout->is_hots]);
            
            session()->flash('success', 'Status HOTS tryout berhasil diubah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status HOTS: ' . $e->getMessage());
        }
    }

    // Method untuk menghitung harga akhir (jika diperlukan di view)
    public function getFinalPriceProperty()
    {
        if ($this->discount) {
            return $this->price - $this->discount;
        }
        return $this->price;
    }
}