<?php

namespace App\Livewire\Admin;

use App\Models\QuestionSubCategory;
use App\Models\QuestionCategory;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionSubCategoriesManage extends Component
{
    use WithPagination;

    public $name, $question_category_id, $is_active = true;
    public $subCategoryId;
    public $isEdit = false;
    public $showModal = false;

    // Untuk filter dan UI
    public $search = '';
    public $showTrashed = false;
    public $perPage = 10;

    protected $queryString = ['search', 'showTrashed'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'question_category_id' => 'required|exists:question_categories,id',
            'is_active' => 'boolean',
        ];
    }

    // Reset pagination agar data tidak nyangkut saat filter berubah
    public function updatedSearch() { $this->resetPage(); }
    public function updatedShowTrashed() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function render()
    {
        $subCategories = QuestionSubCategory::with(['category'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed();
            }, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->oldest()
            ->paginate($this->perPage);

        $categories = QuestionCategory::where('is_active', true)->get();

        return view('livewire.admin.question-sub-categories-manage', [
            'subCategories' => $subCategories,
            'categories' => $categories,
        ])->layout('layouts.admin');
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
        $this->reset(['name', 'question_category_id', 'is_active', 'subCategoryId', 'isEdit']);
        $this->is_active = true;
    }

    public function create()
    {
        $this->validate();

        QuestionSubCategory::create([
            'name' => $this->name,
            'question_category_id' => $this->question_category_id,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->closeModal();
        
        // Dispatch untuk Toast SweetAlert
        $this->dispatch('swal-toast', icon: 'success', title: 'Berhasil!', text: 'Sub kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $subCategory = QuestionSubCategory::withTrashed()->findOrFail($id);
        $this->subCategoryId = $id;
        $this->name = $subCategory->name;
        $this->question_category_id = $subCategory->question_category_id;
        $this->is_active = $subCategory->is_active;
    }

    public function update()
    {
        $this->validate();

        $subCategory = QuestionSubCategory::withTrashed()->findOrFail($this->subCategoryId);
        
        $subCategory->update([
            'name' => $this->name,
            'question_category_id' => $this->question_category_id,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->closeModal();

        // Dispatch untuk Toast SweetAlert
        $this->dispatch('swal-toast', icon: 'success', title: 'Berhasil!', text: 'Sub kategori berhasil diperbarui.');
    }

    // --- LOGIKA AKSI ADMIN (RETURN ARRAY UNTUK SWEETALERT) ---

    public function softDeleteSubCategory($id)
    {
        try {
            $subCategory = QuestionSubCategory::findOrFail($id);
            $subCategory->delete();
            return ['status' => 'success', 'message' => 'Sub kategori berhasil di-soft delete.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus: ' . $e->getMessage()];
        }
    }

    public function restoreSubCategory($id)
    {
        try {
            QuestionSubCategory::withTrashed()->findOrFail($id)->restore();
            return ['status' => 'success', 'message' => 'Sub kategori berhasil dipulihkan.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal memulihkan: ' . $e->getMessage()];
        }
    }

    public function forceDeleteSubCategory($id)
    {
        if (auth()->user()->role !== 'owner') {
            return ['status' => 'error', 'message' => 'Akses ditolak! Hanya Owner yang dapat menghapus permanen.'];
        }

        try {
            $subCategory = QuestionSubCategory::withTrashed()->findOrFail($id);
            $subCategory->forceDelete();
            return ['status' => 'success', 'message' => 'Sub kategori berhasil dihapus secara permanen.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus permanen: ' . $e->getMessage()];
        }
    }
}