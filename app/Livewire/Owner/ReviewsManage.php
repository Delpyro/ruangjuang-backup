<?php

namespace App\Livewire\Owner;

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsManage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $filterStatus = 'all'; 
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

        return view('livewire.owner.reviews-manage', [
            'reviews' => $reviews,
        ])->layout('layouts.owner'); 
    }

    public function toggleStatus($id)
    {
        try {
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
            $review->delete(); 
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

    // KHUSUS OWNER: Fungsi Hapus Permanen
    public function forceDeleteReview($id)
    {
        if (auth()->user()->role !== 'owner') {
            session()->flash('error', 'Akses ditolak! Hanya Owner yang dapat menghapus permanen.');
            return;
        }

        try {
            $review = Review::withTrashed()->findOrFail($id);
            $review->forceDelete(); 
            session()->flash('success', 'Review berhasil dihapus permanen.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }
}