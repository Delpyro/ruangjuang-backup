<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Carbon\Carbon; // Pastikan import Carbon

class TryoutResultPage extends Component
{
    public Tryout $tryout;
    public array $resultsData = [];

    // --- Properti untuk Modal Review ---
    public bool $showReviewModal = false;
    public int $reviewRating = 0;
    public string $reviewText = '';

    // --- Properti untuk kontrol tampilan ---
    public bool $hasReviewed = false;
    
    // --- Properti internal ---
    public $userId;

    /**
     * Mount komponen
     */
    public function mount(Tryout $tryout)
    {
        $this->tryout = $tryout;
        $this->userId = Auth::id();

        // 1. Cek pengerjaan yang sudah selesai
        $allCompletedAttempts = UserTryout::where('id_user', $this->userId)
            ->where('tryout_id', $this->tryout->id)
            ->where('is_completed', true)
            ->orderBy('attempt', 'asc')
            ->get();

        // 2. Jika tidak ada, redirect
        if ($allCompletedAttempts->isEmpty()) {
            session()->flash('error', 'Hasil tryout belum tersedia atau belum Anda selesaikan.');
            return $this->redirect(route('tryout.detail', $tryout->slug));
        }

        // 3. Cek apakah user sudah mereview
        $this->hasReviewed = Review::where('id_user', $this->userId)
                                     ->where('tryout_id', $this->tryout->id)
                                     ->exists();

        // 4. Logika Gated (Terkunci)
        if ($this->hasReviewed) {
            // --- JIKA SUDAH REVIEW ---
            $this->loadAndCalculateResults();
        } else {
            // --- JIKA BELUM REVIEW ---
            $this->showReviewModal = true;
        }
    }

    /**
     * Memuat dan menghitung hasil.
     */
    public function loadAndCalculateResults()
    {
        $this->resultsData = []; 

        $allCompletedAttempts = UserTryout::where('id_user', $this->userId)
            ->where('tryout_id', $this->tryout->id)
            ->where('is_completed', true)
            ->orderBy('attempt', 'asc')
            ->get();

        $allTryoutQuestions = $this->tryout->questions()
                                             ->with(['category', 'subCategory.category'])
                                             ->get();

        foreach ($allCompletedAttempts as $attempt) {
            $this->resultsData[] = $this->calculateResultForAttempt($attempt, $allTryoutQuestions);
        }
    }

    /**
     * Dipanggil saat user klik rating bintang.
     */
    public function setRating($rating)
    {
        $this->reviewRating = (int) $rating;
    }

    /**
     * Dipanggil saat modal disubmit.
     */
    public function submitReview()
    {
        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewText' => 'required|string|min:10|max:1000',
        ], [
            'reviewRating.min' => 'Harap berikan minimal 1 bintang.',
            'reviewText.required' => 'Ulasan tidak boleh kosong.',
            'reviewText.min' => 'Ulasan Anda terlalu singkat (minimal 10 karakter).',
        ]);

        // Simpan review
        Review::updateOrCreate(
            [
                'id_user'   => $this->userId,
                'tryout_id' => $this->tryout->id,
            ],
            [
                'rating'      => $this->reviewRating,
                'review_text' => $this->reviewText,
            ]
        );

        $this->showReviewModal = false;
        $this->hasReviewed = true; 

        // [PENTING] Hitung skor SEKARANG
        $this->loadAndCalculateResults();
    }


    /**
     * Logika inti untuk menghitung total skor dan statistik UNTUK SATU PERCOBAAN.
     */
    private function calculateResultForAttempt(UserTryout $userTryout, EloquentCollection $allTryoutQuestions): array
    {
        // 1. Load jawaban user
        $userAnswers = UserAnswer::where('user_tryout_id', $userTryout->id)
            ->with(['answer', 'question.category']) 
            ->get()
            ->keyBy('question_id');

        // 2. Siapkan variabel kalkulasi
        $totalQuestions = $allTryoutQuestions->count();
        $correctCount = 0;
        $wrongCount = 0;    
        $unansweredCount = 0; 
        $totalScore = 0.0;
        $categorySummary = [];

        // 3. Inisialisasi Kategori
        foreach ($allTryoutQuestions as $question) {
            // [BUG FIX] Prioritaskan kategori dari subCategory. 
            // Mengatasi kasus salah input kategori di CMS di mana soal masuk ke kategori lain.
            $cat = ($question->subCategory && $question->subCategory->category) 
                    ? $question->subCategory->category 
                    : $question->category;
                    
            $categoryId = $cat->id ?? 0;
            $categoryName = $cat->name ?? 'Tanpa Kategori';
            
            if (!isset($categorySummary[$categoryId])) {
                $categorySummary[$categoryId] = [
                    'name' => $categoryName,
                    'total_soal' => 0,
                    'benar' => 0,
                    'salah' => 0,  
                    'kosong' => 0, 
                    'skor_kategori' => 0.0,
                    'percentage' => 0,
                ];
            }
            $categorySummary[$categoryId]['total_soal']++;
        }

        // 4. Proses Jawaban
        foreach ($allTryoutQuestions as $question) {
            $cat = ($question->subCategory && $question->subCategory->category) 
                    ? $question->subCategory->category 
                    : $question->category;
            $categoryId = $cat->id ?? 0;
            
            $userAnswer = $userAnswers->get($question->id); 

            if ($userAnswer && $userAnswer->answer_id) {
                // --- KASUS 1: DIJAWAB ---
                // [BUG FIX #6] Gunakan score tersimpan di DB sebagai sumber kebenaran tunggal.
                // Ini konsisten dengan TryoutWorksheet yang menyimpan points dari tabel answers.
                // Fallback ke answer->points hanya untuk data lama yang score-nya belum terisi.
                $selectedAnswerModel = $userAnswer->answer;
                $pointsEarned = ($userAnswer->score !== null)
                    ? (float) $userAnswer->score
                    : ($selectedAnswerModel ? (float) $selectedAnswerModel->points : 0);

                // Deteksi benar/salah dari nilai skor (>0 = benar, <=0 = salah)
                // agar konsisten dengan semua komponen tanpa bergantung pada is_correct di tabel answers.
                $isCorrect = $pointsEarned > 0;

                if ($isCorrect) {
                    $correctCount++;
                    $categorySummary[$categoryId]['benar']++;
                } else {
                    $wrongCount++;
                    $categorySummary[$categoryId]['salah']++; 
                }
                $totalScore += $pointsEarned;
                $categorySummary[$categoryId]['skor_kategori'] += $pointsEarned;

            } else {
                // --- KASUS 2: TIDAK DIJAWAB ---
                $unansweredCount++;
                $categorySummary[$categoryId]['kosong']++; 
            }
        }
        
        // 5. Hitung persentase per kategori
        foreach ($categorySummary as $categoryId => &$summary) {
            $summary['percentage'] = ($summary['total_soal'] > 0) 
                ? ($summary['benar'] / $summary['total_soal']) * 100 
                : 0;
        }
        unset($summary);

        // 6. Kembalikan hasil kalkulasi
        return [
            'attempt' => $userTryout->attempt,
            'ended_at' => $userTryout->ended_at,

            // [FIX UTAMA] Kita format tanggalnya DI SINI jadi String
            // View tinggal terima beres, gak perlu mikir konversi lagi.
            'tanggal_fix' => $userTryout->ended_at 
                ? Carbon::parse($userTryout->ended_at)
                    ->setTimezone('Asia/Jakarta')
                    ->locale('id')
                    ->isoFormat('LLL') 
                : '-',

            'finalScore' => $totalScore,
            'totalQuestions' => $totalQuestions,
            'totalCorrect' => $correctCount,
            'totalWrong' => $wrongCount,        
            'totalUnanswered' => $unansweredCount, 
            'totalAnswered' => $correctCount + $wrongCount,
            'categoryScores' => array_values($categorySummary),
        ];
    }

    public function goToDiscussion()
    {
        if (!$this->hasReviewed) {
            $this->showReviewModal = true;
            return;
        }
        return $this->redirect(route('tryout.discussion', $this->tryout->slug));
    }

    public function render()
    {
        return view('livewire.customers.tryout-result-page')
            ->layout('layouts.app', ['title' => 'Hasil Tryout: ' . $this->tryout->title]);
    }
}