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
    public $showTrashed = false; 
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all', 'as' => 'status'],
        'showTrashed' => ['except' => false],
    ];

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
                $query->onlyTrashed();
            }, function ($query) {
                $query->whereNull('deleted_at');
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
            $review = Review::withTrashed()->findOrFail($id); 
            $review->update(['is_published' => !$review->is_published]);
            
            // Mengirim toast sukses
            $this->dispatch('swal-toast', icon: 'success', title: 'Berhasil!', text: 'Status publikasi diperbarui.');
        } catch (\Exception $e) {
            $this->dispatch('swal-toast', icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function softDeleteReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();
            return ['status' => 'success', 'message' => 'Review berhasil di-soft delete.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus review: ' . $e->getMessage()];
        }
    }

    public function restoreReview($id)
    {
        try {
            $review = Review::withTrashed()->findOrFail($id);
            $review->restore();
            return ['status' => 'success', 'message' => 'Review berhasil dipulihkan.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal memulihkan review: ' . $e->getMessage()];
        }
    }

    public function forceDeleteReview($id)
    {
        if (auth()->user()->role !== 'owner') {
            return ['status' => 'error', 'message' => 'Akses ditolak! Hanya Owner yang dapat menghapus permanen.'];
        }

        try {
            $review = Review::withTrashed()->findOrFail($id);
            $review->forceDelete(); 
            return ['status' => 'success', 'message' => 'Review berhasil dihapus secara permanen.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal menghapus permanen: ' . $e->getMessage()];
        }
    }
}