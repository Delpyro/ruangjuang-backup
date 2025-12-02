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
    public $confirmingDeletion = false;
    public $subCategoryToDelete;

    // Untuk search
    public $search = '';

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'question_category_id' => 'required|exists:question_categories,id',
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public $showTrashed = false;

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
                $query->whereNull('deleted_at'); // Hanya tampilkan yang tidak terhapus
            })
            // PERUBAHAN: Menggunakan oldest() agar data terlama/tertua menjadi Nomor 1.
            ->oldest()
            ->paginate(10);

        $categories = QuestionCategory::where('is_active', true)->get();

        return view('livewire.admin.question-sub-categories-manage', [
            'subCategories' => $subCategories,
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

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->subCategoryToDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->subCategoryToDelete = null;
    }

    public function delete()
    {
        try {
            $subCategory = QuestionSubCategory::findOrFail($this->subCategoryToDelete);
            $subCategory->delete();
            
            $this->confirmingDeletion = false;
            $this->subCategoryToDelete = null;
            session()->flash('success', 'Sub kategori pertanyaan berhasil dihapus (soft delete).');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus sub kategori: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $subCategory = QuestionSubCategory::withTrashed()->findOrFail($id);
            $subCategory->forceDelete();
            session()->flash('success', 'Sub kategori pertanyaan berhasil dihapus permanen.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            QuestionSubCategory::withTrashed()->findOrFail($id)->restore();
            session()->flash('success', 'Sub kategori pertanyaan berhasil direstore.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal merestore sub kategori: ' . $e->getMessage());
        }
    }
}