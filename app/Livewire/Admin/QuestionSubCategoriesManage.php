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
                $query->onlyTrashed(); // Hanya tampilkan yang terhapus
            }, function ($query) {
                $query->whereNull('deleted_at'); // Hanya tampilkan yang aktif
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
        session()->flash('success', 'Sub kategori pertanyaan berhasil ditambahkan.');
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
        session()->flash('success', 'Sub kategori pertanyaan berhasil diperbarui.');
    }

    // --- LOGIKA AKSI ADMIN (Soft Delete & Restore Only) ---

    public function softDeleteSubCategory($id)
    {
        try {
            $subCategory = QuestionSubCategory::findOrFail($id);
            $subCategory->delete();
            session()->flash('success', 'Sub kategori pertanyaan berhasil di-Soft Delete.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal melakukan soft delete: ' . $e->getMessage());
        }
    }

    public function restoreSubCategory($id)
    {
        try {
            QuestionSubCategory::withTrashed()->findOrFail($id)->restore();
            session()->flash('success', 'Sub kategori pertanyaan berhasil dipulihkan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memulihkan sub kategori: ' . $e->getMessage());
        }
    }
}