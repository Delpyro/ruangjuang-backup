<?php

namespace App\Livewire\Admin;

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsManage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $filterStatus = 'all'; // 'all', 'published', 'hidden'
    public $showTrashed = false; // Tab Aktif/Terhapus
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all', 'as' => 'status'],
        'showTrashed' => ['except' => false],
    ];

    // Reset pagination agar data tidak nyangkut saat filter berubah
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedShowTrashed() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function render()
    {
        $reviews = Review::query()
            ->with(['user:id,name', 'tryout:id,title'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('review_text', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('tryout', function ($tq) {
                            $tq->where('title', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus !== 'all', function ($query) {
                if ($this->filterStatus === 'published') {
                    $query->where('is_published', true);
                } elseif ($this->filterStatus === 'hidden') {
                    $query->where('is_published', false);
                }
            })
            ->when($this->showTrashed, function ($query) {
                $query->onlyTrashed(); // Menampilkan data yang di-Soft Delete
            }, function ($query) {
                $query->whereNull('deleted_at'); // Menampilkan data Aktif
            })
            ->orderBy('created_at', 'desc') 
            ->paginate($this->perPage);

        return view('livewire.admin.reviews-manage', [
            'reviews' => $reviews,
        ])->layout('layouts.admin');
    }

    public function toggleStatus($id)
    {
        try {
            // withTrashed() agar status bisa diubah walau data ada di tab Terhapus
            $review = Review::withTrashed()->findOrFail($id); 
            $review->update(['is_published' => !$review->is_published]);
            session()->flash('success', 'Status review berhasil diperbarui.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function softDeleteReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete(); // Eksekusi Soft Delete
            session()->flash('success', 'Review berhasil di-Soft Delete.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus review: ' . $e->getMessage());
        }
    }

    public function restoreReview($id)
    {
        try {
            $review = Review::withTrashed()->findOrFail($id);
            $review->restore();
            session()->flash('success', 'Review berhasil dipulihkan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memulihkan review: ' . $e->getMessage());
        }
    }
}