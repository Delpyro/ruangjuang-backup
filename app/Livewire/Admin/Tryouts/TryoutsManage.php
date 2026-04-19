<?php

namespace App\Livewire\Admin\Tryouts;

use App\Models\Tryout;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class TryoutsManage extends Component
{
    use WithPagination, WithFileUploads;

    public $title, $slug, $category = 'umum', $is_hots = false, $duration, $content, $quote, $price, $discount, $is_active = true;
    public $tryoutId;
    public $isEdit = false;
    public $showModal = false;
    
    public $confirmingDeletion = false; 
    public $tryoutToDelete;
    
    public $showTrashed = false;
    public $search = '';
    public $filterCategory = ''; 
    public $perPage = 10;

    protected $queryString = ['search', 'showTrashed', 'filterCategory'];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tryouts,slug,' . $this->tryoutId,
            'category' => 'required|in:umum,khusus', 
            'is_hots' => 'boolean',
            'duration' => 'nullable|integer|min:1',
            'content' => 'required|string',
            'quote' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|max:' . $this->price, 
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterCategory() { $this->resetPage(); }
    public function updatedShowTrashed() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    // ✨ FITUR BARU: Dynamic Route Prefix
    public function getRolePrefixProperty()
    {
        return auth()->user()->role; // Output: 'admin' atau 'owner'
    }

    public function updatedTitle($value)
    {
        if (!$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedPrice($value)
    {
        if ($this->discount > $value) {
            $this->discount = $value;
        }
    }

    public function render()
    {
        $tryouts = Tryout::when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category', $this->filterCategory);
            })
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed();
            }, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->oldest() 
            ->paginate($this->perPage);

        return view('livewire.admin.tryouts.tryouts-manage', [
            'tryouts' => $tryouts,
        ])->layout('layouts.admin');
    }

    // --- FUNGSI MODAL BAWAAN (TETAP DIPERTAHANKAN JIKA DIBUTUHKAN) ---
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
        $this->reset(['title', 'slug', 'category', 'is_hots', 'duration', 'content', 'quote', 'price', 'discount', 'is_active', 'tryoutId', 'isEdit']);
        $this->category = 'umum'; 
        $this->is_hots = false;
        $this->is_active = true;
    }

    public function create()
    {
        $this->validate();

        Tryout::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category, 
            'is_hots' => $this->is_hots,
            'duration' => $this->duration,
            'content' => $this->content,
            'quote' => $this->quote,
            'price' => $this->price,
            'discount' => $this->discount, 
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
        $this->category = $tryout->category; 
        $this->is_hots = $tryout->is_hots;
        $this->duration = $tryout->duration;
        $this->content = $tryout->content;
        $this->quote = $tryout->quote;
        $this->price = $tryout->price;
        $this->discount = $tryout->discount; 
        $this->is_active = $tryout->is_active;
    }

    public function update()
    {
        $this->validate();

        $tryout = Tryout::withTrashed()->findOrFail($this->tryoutId);
        
        $tryout->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category, 
            'is_hots' => $this->is_hots,
            'duration' => $this->duration,
            'content' => $this->content,
            'quote' => $this->quote,
            'price' => $this->price,
            'discount' => $this->discount, 
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'Tryout berhasil diperbarui.');
    }

    // --- FUNGSI AKSI UTAMA (SUDAH DIUBAH MENJADI RETURN ARRAY) ---
    
    public function softDeleteTryout($id)
    {
        try {
            $tryout = Tryout::findOrFail($id);
            $tryout->delete();
            return ['status' => 'success', 'message' => 'Tryout "' . $tryout->title . '" berhasil di-soft delete.'];
        } catch (\Exception $e) { 
            return ['status' => 'error', 'message' => 'Gagal menghapus tryout: ' . $e->getMessage()];
        }
    }

    public function restoreTryout($id) 
    {
        try {
            $tryout = Tryout::withTrashed()->findOrFail($id);
            $tryout->restore();
            return ['status' => 'success', 'message' => 'Tryout "' . $tryout->title . '" berhasil direstore.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal merestore tryout: ' . $e->getMessage()];
        }
    }

    public function forceDeleteTryout($id) 
    {
        if (auth()->user()->role !== 'owner') {
            return ['status' => 'error', 'message' => 'Akses ditolak. Hanya owner yang dapat menghapus permanen.'];
        }

        try {
            $tryout = Tryout::withTrashed()->findOrFail($id);
            $tryout->forceDelete();
            return ['status' => 'success', 'message' => 'Tryout berhasil dihapus permanen.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus permanen: ' . $e->getMessage()];
        }
    }

    // --- FUNGSI TOGGLES ---
    public function toggleStatus($id)
    {
        try {
            $tryout = Tryout::withTrashed()->findOrFail($id); 
            $tryout->update(['is_active' => !$tryout->is_active]);
            session()->flash('success', 'Status tryout berhasil diubah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function toggleHots($id)
    {
        try {
            $tryout = Tryout::withTrashed()->findOrFail($id); 
            $tryout->update(['is_hots' => !$tryout->is_hots]);
            session()->flash('success', 'Status HOTS tryout berhasil diubah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status HOTS: ' . $e->getMessage());
        }
    }
}