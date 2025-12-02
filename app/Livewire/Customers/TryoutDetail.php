<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\Ranking;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\WithPagination; 

class TryoutDetail extends Component
{
    use WithPagination; 

    protected $paginationTheme = 'tailwind';

    public Tryout $data;
    public $userTryoutHistory;
    public $selectedAttempt = null;

    // --- Properti untuk data tambahan ---
    public $rankings; // Menyimpan Top 10 Peringkat
    public $averageRating = 0;
    public $totalReviews = 0;
    
    public $userRanking = null;      // Data Object Ranking User
    public $userRankPosition = null; // Angka Urutan Peringkat (1, 2, 15, dst)
    
    /**
     * Mount komponen dan memuat data tryout.
     */
    public function mount($tryout) 
    {
        // Muat tryout berdasarkan slug
        $this->data = Tryout::where('slug', $tryout)->firstOrFail();
        
        // --- 1. Ambil 10 Peringkat Teratas ---
        $this->rankings = Ranking::where('tryout_id', $this->data->id)
                                    ->with('user') 
                                    ->orderBy('score', 'desc')
                                    ->limit(10) 
                                    ->get();

        // --- 2. Statistik Review ---
        $baseReviewQuery = Review::where('tryout_id', $this->data->id)
                                 ->where('is_published', true);

        $this->totalReviews = $baseReviewQuery->count();
        $this->averageRating = $this->totalReviews > 0 ? $baseReviewQuery->avg('rating') : 0;

        // --- 3. Logika Peringkat User (FIXED) ---
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Cek apakah user ada di dalam koleksi Top 10 (Memory Check)
            // Search index mengembalikan urutan array (mulai dari 0)
            $indexInTop10 = $this->rankings->search(function ($item) use ($userId) {
                return $item->id_user === $userId;
            });

            if ($indexInTop10 !== false) {
                // KASUS A: User Masuk Top 10
                $this->userRanking = $this->rankings[$indexInTop10];
                $this->userRankPosition = $indexInTop10 + 1; // Array 0 = Juara 1
            } else {
                // KASUS B: User Diluar Top 10 -> Ambil dari DB
                $this->userRanking = Ranking::where('tryout_id', $this->data->id)
                                            ->where('id_user', $userId)
                                            ->with('user')
                                            ->first();

                // Jika data ranking ditemukan, hitung posisinya secara manual
                if ($this->userRanking) {
                    // Hitung ada berapa orang yang skornya LEBIH BESAR dari skor saya
                    $higherRankCount = Ranking::where('tryout_id', $this->data->id)
                                              ->where('score', '>', $this->userRanking->score)
                                              ->count();
                    
                    // Peringkat saya = Jumlah orang di atas saya + 1
                    $this->userRankPosition = $higherRankCount + 1;
                }
            }
        }
        
        // Muat riwayat pengerjaan user
        $this->loadUserTryoutHistory();
    }
    
    /**
     * Helper untuk memuat riwayat pengerjaan user.
     */
    protected function loadUserTryoutHistory()
    {
        if (Auth::check()) {
            $this->userTryoutHistory = UserTryout::where('id_user', Auth::id())
                                                 ->where('tryout_id', $this->data->id)
                                                 ->orderBy('attempt', 'asc')
                                                 ->get();
        } else {
            $this->userTryoutHistory = collect();
        }
    }
    
    /**
     * Dipanggil saat user mengklik "Mulai Percobaan Ke-X".
     */
    public function confirmStart($attemptNumber)
    {
        $this->selectedAttempt = $attemptNumber;
        $this->dispatch('show-copyright-modal');
    }
    
    /**
     * Dipanggil setelah user mengklik "Saya Mengerti & Lanjutkan" di modal.
     */
    public function startAttempt()
    {
        $attemptNumber = $this->selectedAttempt;
        
        if (!$attemptNumber) {
            session()->flash('error', 'Kesalahan internal: Nomor percobaan tidak ditemukan.');
            return;
        }

        $user = Auth::user();
        $userTryout = UserTryout::where('id_user', $user->id)
                                ->where('tryout_id', $this->data->id)
                                ->where('attempt', $attemptNumber)
                                ->first();
        
        if (!$userTryout) {
            session()->flash('error', 'Gagal memulai. Sesi tryout percobaan ke-' . $attemptNumber . ' tidak ditemukan.');
            return;
        }

        if ($userTryout->is_completed) {
            session()->flash('error', 'Percobaan ini sudah selesai dan tidak dapat diulang.');
            return;
        }
        
        // Set waktu mulai jika belum ada
        if (is_null($userTryout->started_at)) {
            $now = Carbon::now();
            $endTime = $now->copy()->addMinutes($this->data->duration);

            $userTryout->update([
                'started_at' => $now,
                'ended_at' => $endTime,
                'is_completed' => false,
            ]);
        } else {
            // Cek jika waktu sudah habis saat mencoba melanjutkan
            if (Carbon::now()->isAfter($userTryout->ended_at)) {
                $userTryout->update(['is_completed' => true]); 
                session()->flash('error', 'Waktu pengerjaan untuk percobaan ini sudah habis.');
                return;
            }
        }
        
        $redirectUrl = route('tryout.start', [
            'tryout' => $this->data->slug,
            'attempt' => $attemptNumber
        ]);
        
        $this->selectedAttempt = null; 
        
        return $this->redirect($redirectUrl, navigate: false);
    }

    public function updatedPage()
    {
        $this->dispatch('review-page-changed');
    }

    public function render()
    {
        $reviews = Review::where('tryout_id', $this->data->id)
                         ->where('is_published', true)
                         ->with('user')
                         ->orderBy('created_at', 'desc')
                         ->paginate(5); 

        return view('livewire.customers.tryout-detail', [
            'userTryout' => $this->userTryoutHistory,
            'reviews' => $reviews, 
        ])
        ->layout('layouts.app');
    }
}