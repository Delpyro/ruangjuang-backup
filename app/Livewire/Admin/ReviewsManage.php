<?php

namespace App\Livewire\Admin; // <-- Perbaikan namespace

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsManage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // --- Properti untuk Deletion ---
    public $confirmingDeletion = false;
    public $reviewToDelete;

    // --- Properti untuk Filtering & Searching ---
    public $search = '';
    public $filterStatus = 'all'; // 'all', 'published', 'hidden'

    /**
     * Menghubungkan filter ke query string URL.
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all', 'as' => 'status'],
    ];

    /**
     * Reset halaman saat melakukan pencarian.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset halaman saat mengubah filter status.
     */
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Render komponen.
     */
    public function render()
    {
        $reviews = Review::query()
            ->with(['user:id,name', 'tryout:id,title']) // Eager load (dioptimalkan)
            ->when($this->search, function ($query) {
                // Pencarian
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
            ->when($this->filterStatus, function ($query) {
                // Filter berdasarkan status
                if ($this->filterStatus === 'published') {
                    $query->where('is_published', true);
                } elseif ($this->filterStatus === 'hidden') {
                    $query->where('is_published', false);
                }
                // Jika 'all', tidak perlu filter
            })
            // PENGURUTAN: Tetap 'desc' agar review terbaru muncul paling atas (sesuai permintaan).
            ->orderBy('created_at', 'desc') 
            ->paginate(10); // Pagination

        return view('livewire.admin.reviews-manage', [
            'reviews' => $reviews,
        ])->layout('layouts.admin'); // Menggunakan layout admin
    }

    /**
     * [LOGIKA UTAMA]
     * Toggle status 'is_published' dari sebuah review.
     * Dipanggil langsung dari tombol di tabel.
     */
    public function toggleStatus(Review $review)
    {
        try {
            $review->is_published = !$review->is_published; // Balikkan nilainya
            $review->save();
            session()->flash('success', 'Status review berhasil diperbarui.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan modal konfirmasi penghapusan.
     */
    public function confirmDelete($id)
    {
        $this->reviewToDelete = $id;
        $this->confirmingDeletion = true;
    }

    /**
     * Membatalkan proses penghapusan.
     */
    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->reviewToDelete = null;
    }

    /**
     * Menghapus review secara permanen.
     */
    public function delete()
    {
        try {
            Review::findOrFail($this->reviewToDelete)->delete();
            session()->flash('success', 'Review berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus review: ' . $e->getMessage());
        }
        
        $this->confirmingDeletion = false;
        $this->reviewToDelete = null;
    }
}