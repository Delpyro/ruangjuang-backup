<?php

namespace App\Livewire\Owner;

use App\Models\QuestionCategory;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionCategoriesManage extends Component
{
    use WithPagination;

    public $name, $passing_grade = 100.00, $is_active = true;
    public $categoryId;
    public $isEdit = false;
    public $showModal = false;
    
    // Properti untuk filter dan UI
    public $search = '';
    public $showTrashed = false;
    public $perPage = 10;

    protected $queryString = ['search', 'showTrashed'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'passing_grade' => 'required|numeric|min:0|max:200',
            'is_active' => 'boolean',
        ];
    }

    // Reset pagination agar data tidak nyangkut saat filter berubah
    public function updatedSearch() { $this->resetPage(); }
    public function updatedShowTrashed() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function render()
    {
        $categories = QuestionCategory::with(['subCategories'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed(); // Menampilkan data yang di-Soft Delete
            }, function ($query) {
                $query->whereNull('deleted_at'); // Menampilkan data Aktif
            })
            ->oldest() 
            ->paginate($this->perPage);

        return view('livewire.owner.question-categories-manage', [
            'categories' => $categories,
        ])->layout('layouts.owner'); // Menggunakan Layout Owner
    }

    // --- MODAL CREATE & EDIT ---
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
        $this->reset(['name', 'passing_grade', 'is_active', 'categoryId', 'isEdit']);
        $this->passing_grade = 100.00;
        $this->is_active = true;
    }

    public function create()
    {
        $this->validate();

        QuestionCategory::create([
            'name' => $this->name,
            'passing_grade' => $this->passing_grade,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'Kategori pertanyaan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $category = QuestionCategory::withTrashed()->findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->passing_grade = $category->passing_grade;
        $this->is_active = $category->is_active;
    }

    public function update()
    {
        $this->validate();

        $category = QuestionCategory::withTrashed()->findOrFail($this->categoryId);
        
        $category->update([
            'name' => $this->name,
            'passing_grade' => $this->passing_grade,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('success', 'Kategori pertanyaan berhasil diperbarui.');
    }

    // --- LOGIKA AKSI OWNER (Soft Delete, Restore, Force Delete) ---

    private function hasSubCategories($categoryId)
    {
        $category = QuestionCategory::withTrashed()->with(['subCategories'])->find($categoryId);
        return $category && $category->subCategories->count() > 0;
    }

    public function softDeleteCategory($id)
    {
        try {
            if ($this->hasSubCategories($id)) {
                session()->flash('error', 'Kategori tidak dapat dihapus karena masih memiliki subkategori.');
                return;
            }

            $category = QuestionCategory::findOrFail($id);
            $category->delete();
            session()->flash('success', 'Kategori pertanyaan berhasil di-Soft Delete.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    public function restoreCategory($id)
    {
        try {
            QuestionCategory::withTrashed()->findOrFail($id)->restore();
            session()->flash('success', 'Kategori pertanyaan berhasil dipulihkan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memulihkan kategori: ' . $e->getMessage());
        }
    }

    // KHUSUS OWNER: Hapus Permanen
    public function forceDeleteCategory($id)
    {
        if (auth()->user()->role !== 'owner') {
            session()->flash('error', 'Akses ditolak! Hanya Owner yang dapat menghapus permanen.');
            return;
        }

        try {
            if ($this->hasSubCategories($id)) {
                session()->flash('error', 'Kategori tidak dapat dihapus permanen karena masih memiliki subkategori.');
                return;
            }

            $category = QuestionCategory::withTrashed()->findOrFail($id);
            $category->forceDelete(); 
            session()->flash('success', 'Kategori pertanyaan berhasil dihapus permanen.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }
}