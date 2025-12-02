<?php

namespace App\Livewire\Admin;

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
    public $confirmingDeletion = false;
    public $categoryToDelete;
    
    // Tambahkan untuk konfirmasi delete permanen
    public $confirmingForceDelete = false;
    public $categoryToForceDelete;

    // Untuk search
    public $search = '';

    // Property untuk error message
    public $errorMessage = '';

    protected $queryString = ['search'];

    // Tambahkan property untuk showTrashed
    public $showTrashed = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'passing_grade' => 'required|numeric|min:0|max:200',
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = QuestionCategory::with(['subCategories'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed(); // Hanya tampilkan yang terhapus
            }, function ($query) {
                $query->whereNull('deleted_at'); // Hanya tampilkan yang tidak terhapus
            })
            // PERUBAHAN UTAMA: Menggunakan oldest() agar data terlama/tertua (yang pertama kali dibuat) menjadi Nomor 1.
            ->oldest() 
            ->paginate(10);

        return view('livewire.admin.question-categories-manage', [
            'categories' => $categories,
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
        $this->reset(['name', 'passing_grade', 'is_active', 'categoryId', 'isEdit', 'errorMessage']);
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
        // Tidak perlu resetPage() karena data baru akan muncul di halaman terakhir.
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

    // Method untuk mengecek apakah kategori memiliki subkategori
    private function hasSubCategories($categoryId)
    {
        $category = QuestionCategory::with(['subCategories'])->find($categoryId);
        return $category && $category->subCategories->count() > 0;
    }

    public function confirmDelete($id)
    {
        // Cek apakah kategori memiliki subkategori
        if ($this->hasSubCategories($id)) {
            $this->errorMessage = 'Kategori tidak dapat dihapus karena masih memiliki subkategori. Harap hapus atau pindahkan semua subkategori terlebih dahulu.';
            $this->confirmingDeletion = false;
            return;
        }

        $this->errorMessage = '';
        $this->confirmingDeletion = true;
        $this->categoryToDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->categoryToDelete = null;
        $this->errorMessage = '';
    }

    public function delete()
    {
        try {
            // Double check sebelum menghapus
            if ($this->hasSubCategories($this->categoryToDelete)) {
                session()->flash('error', 'Kategori tidak dapat dihapus karena masih memiliki subkategori.');
                $this->confirmingDeletion = false;
                $this->categoryToDelete = null;
                return;
            }

            $category = QuestionCategory::findOrFail($this->categoryToDelete);
            $category->delete();
            
            $this->confirmingDeletion = false;
            $this->categoryToDelete = null;
            session()->flash('success', 'Kategori pertanyaan berhasil dihapus (soft delete).');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    // Method untuk konfirmasi force delete dengan pengecekan
    public function confirmForceDelete($id)
    {
        // Untuk force delete, kita juga perlu cek apakah ada subkategori
        $category = QuestionCategory::withTrashed()->with(['subCategories'])->find($id);
        
        if ($category && $category->subCategories->count() > 0) {
            $this->errorMessage = 'Kategori tidak dapat dihapus permanen karena masih memiliki subkategori. Harap hapus atau pindahkan semua subkategori terlebih dahulu.';
            $this->confirmingForceDelete = false;
            return;
        }

        $this->errorMessage = '';
        $this->confirmingForceDelete = true;
        $this->categoryToForceDelete = $id;
    }

    public function cancelForceDelete()
    {
        $this->confirmingForceDelete = false;
        $this->categoryToForceDelete = null;
        $this->errorMessage = '';
    }

    public function forceDelete()
    {
        try {
            // Double check sebelum force delete
            $category = QuestionCategory::withTrashed()->with(['subCategories'])->find($this->categoryToForceDelete);
            
            if ($category && $category->subCategories->count() > 0) {
                session()->flash('error', 'Kategori tidak dapat dihapus permanen karena masih memiliki subkategori.');
                $this->confirmingForceDelete = false;
                $this->categoryToForceDelete = null;
                return;
            }

            $category->forceDelete();
            
            $this->confirmingForceDelete = false;
            $this->categoryToForceDelete = null;
            session()->flash('success', 'Kategori pertanyaan berhasil dihapus permanen.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            QuestionCategory::withTrashed()->findOrFail($id)->restore();
            session()->flash('success', 'Kategori pertanyaan berhasil direstore.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal merestore kategori: ' . $e->getMessage());
        }
    }
}