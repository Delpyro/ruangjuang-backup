<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Question;
use App\Models\Answer; // <-- Tambahan Model Answer
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class TryoutDiscussionWorksheet extends Component
{
    public Tryout $tryout;
    public ?UserTryout $userTryout = null;
    
    public array $questionIds = []; 
    public ?Question $currentQuestion = null; 
    public int $currentQuestionIndex = 0; 
    public int $totalQuestions = 0;
    
    // Properti Riwayat Jawaban untuk SEMUA sesi
    // Format: [ question_id => [ [ 'user_tryout_id' => X, 'answer_id' => Y, 'attempt_number' => Z ], ... ] ]
    public array $answerHistory = []; 

    /**
     * Mount komponen, validasi, dan inisialisasi data.
     */
    public function mount(Tryout $tryout)
    {
        $this->tryout = $tryout;

        // 1. Dapatkan sesi pengerjaan TERAKHIR yang sudah selesai (completed)
        $this->userTryout = UserTryout::where('id_user', Auth::id())
                                        ->where('tryout_id', $this->tryout->id)
                                        ->where('is_completed', true)
                                        ->latest('ended_at') 
                                        ->first();

        // 2. Validasi sesi
        if (!$this->userTryout) {
            session()->flash('error', 'Sesi pembahasan tidak tersedia. Tryout belum selesai atau tidak ditemukan.');
            return $this->redirect(route('tryout.detail', $tryout->slug));
        }
        
        // --- Logika Load Soal dan Progres ---
        if (! $this->loadQuestions()) {
            return;
        }
        
        // Memuat SEMUA riwayat jawaban dari semua sesi Tryout yang selesai
        $this->loadAnswerHistory(); 
        
        $this->setCurrentQuestion();
    }

    // ----------------------------------------------------------------------
    // DATA LOADERS
    // ----------------------------------------------------------------------

    /**
     * Helper: Memuat semua soal dan menyimpan hanya ID-nya.
     */
    private function loadQuestions(): bool
    {
        $questionModels = $this->tryout->activeQuestions() 
                                        ->select('id', 'id_question_categories', 'id_question_sub_category')
                                        ->get();
        
        $this->questionIds = $questionModels->pluck('id')->toArray();
        $this->totalQuestions = count($this->questionIds);

        if ($this->totalQuestions == 0) {
            session()->flash('error', 'Tryout ini belum memiliki soal aktif.');
            return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true); 
        }
        
        return true;
    }

    /**
     * Memuat riwayat jawaban dari SEMUA sesi Tryout yang selesai
     */
    private function loadAnswerHistory()
    {
        $user = Auth::user();
        
        // Dapatkan semua sesi tryout yang sudah selesai
        $completedSessions = UserTryout::where('id_user', $user->id)
                                        ->where('tryout_id', $this->tryout->id)
                                        ->where('is_completed', true)
                                        ->orderBy('ended_at', 'asc')
                                        ->get(['id']);

        if ($completedSessions->isEmpty()) {
            return;
        }

        $sessionIds = $completedSessions->pluck('id')->toArray();
        
        // Ambil semua jawaban yang dibuat dalam sesi-sesi ini
        $allAnswers = UserAnswer::whereIn('user_tryout_id', $sessionIds)
                                ->whereIn('question_id', $this->questionIds)
                                ->get(['user_tryout_id', 'question_id', 'answer_id']);

        $history = [];
        
        // Kelompokkan sesi untuk mengetahui urutan pengerjaan (Attempt 1, 2, 3, dst.)
        $sessionOrderMap = array_flip($sessionIds); 

        foreach ($allAnswers as $answer) {
            $questionId = $answer->question_id;
            
            // Tentukan urutan pengerjaan (1-based index)
            $attemptNumber = $sessionOrderMap[$answer->user_tryout_id] + 1;

            if (!isset($history[$questionId])) {
                $history[$questionId] = [];
            }

            // Simpan riwayat jawaban untuk setiap soal
            $history[$questionId][] = [
                'user_tryout_id' => $answer->user_tryout_id,
                'answer_id' => $answer->answer_id,
                'attempt_number' => $attemptNumber,
            ];
        }

        $this->answerHistory = $history;
    }


    /**
     * Helper: Mengatur soal saat ini berdasarkan index dan me-load model dari DB.
     */
    private function setCurrentQuestion()
    {
        if ($this->currentQuestionIndex < 0) {
            $this->currentQuestionIndex = 0;
        }
        if ($this->currentQuestionIndex >= $this->totalQuestions) {
            $this->currentQuestionIndex = $this->totalQuestions - 1;
        }

        $questionId = $this->questionIds[$this->currentQuestionIndex] ?? null;

        if (!$questionId) {
            $this->currentQuestion = null;
            return;
        }

        // RE-FETCH Model dengan semua relasi yang dibutuhkan
        $this->currentQuestion = Question::with(['answers', 'correctAnswer', 'category', 'subCategory'])->find($questionId);

        if (! $this->currentQuestion) {
            session()->flash('error', 'Gagal memuat soal pembahasan.');
        }
    }

    // ----------------------------------------------------------------------
    // NAVIGATION METHODS
    // ----------------------------------------------------------------------

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
            $this->setCurrentQuestion();
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->setCurrentQuestion();
        }
    }

    public function navigateToQuestion($index)
    {
        $this->currentQuestionIndex = (int) $index;
        $this->setCurrentQuestion();
    }
    
    // ----------------------------------------------------------------------
    // VIEW HELPERS (Untuk Sidebar dan View Blade)
    // ----------------------------------------------------------------------

    /**
     * Helper: Mendapatkan status dan class Tailwind untuk nomor soal di sidebar.
     * @param int $index Index soal (0-based).
     * @return string Class Tailwind CSS.
     */
    public function getQuestionStatusClass($index): string
    {
        if (!isset($this->questionIds[$index])) {
            return 'bg-gray-400'; // Soal tidak ditemukan
        }
        
        $questionId = $this->questionIds[$index];

        // 1. Tentukan Status Aktif (Border Biru Langit)
        $activeClass = '';
        if ($this->currentQuestionIndex === $index) {
            $activeClass = ' z-10 scale-105 border-2 border-cyan-400 ring-2 ring-cyan-200 shadow-lg'; 
        }

        // 2. Tentukan Status Jawaban
        $questionHistory = $this->answerHistory[$questionId] ?? []; 
        $latestAnswer = $questionHistory[array_key_last($questionHistory)] ?? null;
        
        $userAnswerId = $latestAnswer['answer_id'] ?? null;

        // Default ke Merah (Belum Jawab/Salah)
        $baseBgClass = 'bg-red-600'; 
        
        if ($userAnswerId !== null) {
            // Cek langsung ke database apakah jawaban ini benar
            $isCorrect = Answer::where('id', $userAnswerId)->value('is_correct');
            
            if ($isCorrect) {
                // Jawaban Benar -> Hijau (Sesuai standar config kamu)
                $baseBgClass = 'bg-answered';
            } else {
                // Jawaban Salah -> Merah
                $baseBgClass = 'bg-red-600';
            }
        }
        
        return $baseBgClass . $activeClass;
    }

    public function getCurrentQuestionAnswerHistory(): array
    {
        if (!$this->currentQuestion) {
            return [];
        }
        
        return $this->answerHistory[$this->currentQuestion->id] ?? [];
    }

    public function getCorrectAnswerId(): ?int
    {
        if (!$this->currentQuestion) {
            return null;
        }
        
        // Cari jawaban yang benar dari relasi answers yang sudah ter-load
        $correctAnswer = $this->currentQuestion->answers->where('is_correct', true)->first();
        
        return $correctAnswer ? $correctAnswer->id : null;
    }

    public function getCurrentSubCategoryName(): string
    {
        return optional(optional($this->currentQuestion)->subCategory)->name ?? 'Tanpa Kategori';
    }

    public function render()
    {
        return view('livewire.customers.tryout-discussion-worksheet')
                        ->layout('layouts.blank', ['title' => 'Pembahasan: ' . $this->tryout->title]);
    }
}