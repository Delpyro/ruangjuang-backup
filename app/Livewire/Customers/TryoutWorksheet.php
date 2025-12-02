<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Question;
use App\Models\Ranking; 
use App\Models\TryoutCategoryScore; 
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class TryoutWorksheet extends Component
{
    public Tryout $tryout;
    public $userTryout; 
    public $endTime; 
    public $title; 

    // --- Properti Soal ---
    public array $questionIds = [];
    public ?Question $currentQuestion = null;
    public int $currentQuestionIndex = 0;
    public int $totalQuestions = 0;
    
    public array $userAnswers = [];

    // --- Properti Live ---
    public $selectedAnswerId = null; 
    public bool $isDoubtful = false;
    
    // Properti untuk melacak state 'original'
    private $originalAnswerId = null;
    private bool $originalDoubtful = false;
    
    public float $progressPercent = 0;

    
    // ----------------------------------------------------------------------
    // INIT & SETUP 
    // ----------------------------------------------------------------------

    /**
     * Mount komponen.
     */
    public function mount(Tryout $tryout, $attempt = 1)
    {
        $this->tryout = $tryout;
        $this->title = 'Pengerjaan Tryout: ' . $this->tryout->title;

        $userTryout = UserTryout::where('id_user', Auth::id())
                                ->where('tryout_id', $this->tryout->id)
                                ->where('attempt', $attempt) 
                                ->first();

        if (!$userTryout) {
            session()->flash('error', 'Sesi tryout (attempt ' . $attempt . ') tidak ditemukan.');
            return $this->redirect(route('tryout.my-tryouts'));
        }
        
        if ($userTryout->is_completed) {
            session()->flash('info', 'Attempt ini sudah selesai. Melihat hasil.');
            return $this->redirect(route('tryout.my-results', $this->tryout->slug));
        }

        if (!$userTryout->started_at) {
            session()->flash('error', 'Tryout belum dimulai. Silakan mulai tryout Anda dari halaman My Tryouts.');
            return $this->redirect(route('tryout.my-tryouts'));
        }

        $this->userTryout = $userTryout;
        
        // Cek apakah waktu sudah habis secara real-time
        if (Carbon::now()->isAfter($userTryout->ended_at)) {
            $this->forceFinishExam($userTryout); 
            session()->flash('error', 'Waktu pengerjaan tryout ini sudah habis. Jawaban Anda disimpan dan dinilai.');
            return $this->redirect(route('tryout.my-results', $this->tryout->slug));
        }

        $this->endTime = $userTryout->ended_at->toIso8601String();
        
        if (! $this->loadQuestions()) {
            return;
        }
        
        $this->loadUserProgress();
        $this->setCurrentQuestion();
        $this->updateProgress();
        
        if (session()->has('tryout_timer_data')) {
            $timerData = session()->get('tryout_timer_data');
            $this->dispatch('save-timer-to-storage', $timerData);
            session()->forget('tryout_timer_data');
        }
    }

    private function loadQuestions(): bool
    {
        $questionModels = $this->tryout->activeQuestions() 
                                    ->select('id') 
                                    ->get();
        
        $this->questionIds = $questionModels->pluck('id')->toArray();
        $this->totalQuestions = count($this->questionIds);

        if ($this->totalQuestions == 0) {
            session()->flash('error', 'Tryout ini belum memiliki soal aktif.');
            $this->redirect(route('tryout.my-tryouts'));
            return false;
        }
        
        return true;
    }

    private function loadUserProgress()
    {
        $answers = UserAnswer::where('user_tryout_id', $this->userTryout->id) 
                           ->whereIn('question_id', $this->questionIds)
                           ->get();
        
        $this->userAnswers = $answers->keyBy('question_id')->map(function ($answer) {
            return [
                'answer_id' => $answer->answer_id,
                'is_doubtful' => $answer->is_doubtful,
            ];
        })->toArray();
    }

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
            session()->flash('error', 'Gagal memuat soal. Silakan muat ulang halaman.');
            return;
        }

        $this->currentQuestion = Question::with(['answers', 'subCategory'])->find($questionId);
        
        if (! $this->currentQuestion) {
            session()->flash('error', 'Gagal memuat soal. Silakan muat ulang halaman.');
            return;
        }

        $savedAnswer = $this->userAnswers[$this->currentQuestion->id] ?? null;

        $this->selectedAnswerId = $savedAnswer['answer_id'] ?? null;
        $this->isDoubtful = $savedAnswer['is_doubtful'] ?? false;
        
        $this->originalAnswerId = $this->selectedAnswerId;
        $this->originalDoubtful = $this->isDoubtful;
        
        $this->resetErrorBag();
    }

    // ----------------------------------------------------------------------
    // ACTION METHODS 
    // ----------------------------------------------------------------------

    public function saveAnswer()
    {
        $isAnswerChanged = ($this->selectedAnswerId != $this->originalAnswerId);
        $isDoubtfulChanged = ($this->isDoubtful != $this->originalDoubtful);

        if (!$isAnswerChanged && !$isDoubtfulChanged) {
            return; 
        }

        if (!$this->currentQuestion || !$this->userTryout) {
            return;
        }

        $points = 0; 

        if ($this->selectedAnswerId) {
            if (!$this->currentQuestion->relationLoaded('answers')) {
                $this->currentQuestion->load('answers');
            }
            $selected = $this->currentQuestion->answers->find($this->selectedAnswerId);
            $points = $selected->points ?? 0;
        }

        UserAnswer::updateOrCreate(
            [
                'user_tryout_id' => $this->userTryout->id, 
                'question_id' => $this->currentQuestion->id
            ],
            [
                'id_user' => Auth::id(), 
                'answer_id' => $this->selectedAnswerId,
                'is_doubtful' => $this->isDoubtful,
                'score' => $points 
            ]
        );

        $this->userAnswers[$this->currentQuestion->id] = [
            'answer_id' => $this->selectedAnswerId,
            'is_doubtful' => $this->isDoubtful,
        ];
        
        $this->originalAnswerId = $this->selectedAnswerId;
        $this->originalDoubtful = $this->isDoubtful;

        $this->updateProgress();
    }

    public function showFinishConfirmation()
    {
        $this->dispatch('show-finish-alert');
    }

    public function saveAndNext()
    {
        $this->saveAnswer();
        
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
            $this->setCurrentQuestion();
        }
    }

    public function previousQuestion()
    {
        $this->saveAnswer();
        
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->setCurrentQuestion();
        }
    }

    public function navigateToQuestion($index)
    {
        $this->saveAnswer();
        $this->currentQuestionIndex = $index;
        $this->setCurrentQuestion();
    }
    
    public function skipQuestion()
    {
        $this->saveAnswer();
        
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
            $this->setCurrentQuestion(); 
        }
    }
    
    public function finishExam()
    {
        $this->saveAnswer(); 
        $this->forceFinishExam($this->userTryout); 
        
        session()->flash('success', 'Tryout telah berhasil diselesaikan. Menghitung hasil...');
        return $this->redirect(route('tryout.my-results', $this->tryout->slug)); 
    }
    
    // ----------------------------------------------------------------------
    // HOOKS 
    // ----------------------------------------------------------------------

    public function updatedIsDoubtful($value)
    {
        $this->isDoubtful = (bool) $value;
    }

    public function updatedSelectedAnswerId($value)
    {
        $this->selectedAnswerId = $value;
        if($value !== null) {
            $this->isDoubtful = false;
        }
    }

    // ----------------------------------------------------------------------
    // HELPERS 
    // ----------------------------------------------------------------------

    private function updateProgress()
    {
        $answeredCount = 0;
        foreach ($this->userAnswers as $answer) {
            if ($answer['answer_id'] !== null) {
                $answeredCount++;
            }
        }
        
        if ($this->totalQuestions > 0) {
            $this->progressPercent = round(($answeredCount / $this->totalQuestions) * 100);
        }
    }
    
    /**
     * [LOGIKA DIPERBAIKI - FIX BUG WAKTU]
     * Helper untuk menandai tryout selesai + MENYIMPAN RANKING + MENYIMPAN RAPOR.
     */
    private function forceFinishExam(UserTryout $userTryout)
    {
        // 1. Tandai ujian sebagai selesai
        $userTryout->is_completed = true;
        
        // [PERBAIKAN UTAMA DI SINI]
        // Kita HAPUS pengecekan if(!$userTryout->ended_at)
        // Kita paksa 'ended_at' diupdate menjadi WAKTU SEKARANG (Realtime)
        // Agar menimpa waktu deadline yang tersimpan sebelumnya.
        $userTryout->ended_at = Carbon::now(); 

        $userTryout->save();

        //
        // --- LOGIKA RANKING & RAPOR ---
        //
        
        // 2. Cek apakah ini PERCOBAAN PERTAMA.
        if ($userTryout->attempt == 1) {
            
            // --- Logika Kalkulasi Lengkap ---
            
            // Ambil semua soal aktif untuk tryout ini, lengkap dengan info kategori
            $allTryoutQuestions = $this->tryout->activeQuestions()
                                             ->with('category')
                                             ->get();
            
            // Ambil semua jawaban user untuk pengerjaan ini
            $userAnswers = UserAnswer::where('user_tryout_id', $userTryout->id)
                                     ->with(['answer', 'question.category']) 
                                     ->get()
                                     ->keyBy('question_id');

            $categorySummary = [];
            $totalScore = 0.0;
            
            // Inisialisasi Kategori (berdasarkan SEMUA soal)
            foreach ($allTryoutQuestions as $question) {
                $categoryId = $question->category->id ?? 0;
                if (!isset($categorySummary[$categoryId])) {
                    $categorySummary[$categoryId] = [
                        'category_id' => $categoryId, 
                        'total_soal' => 0,
                        'skor_kategori' => 0.0,
                        'benar' => 0,
                        'salah' => 0,
                        'kosong' => 0,
                    ];
                }
                $categorySummary[$categoryId]['total_soal']++;
            }

            // Proses Jawaban
            foreach ($allTryoutQuestions as $question) {
                $categoryId = $question->category->id ?? 0;
                $userAnswer = $userAnswers->get($question->id);

                if ($userAnswer && $userAnswer->answer_id) {
                    // --- KASUS 1: DIJAWAB ---
                    $selectedAnswerModel = $userAnswer->answer;
                    $pointsEarned = $userAnswer->score ?? 0;
                    $isCorrect = $selectedAnswerModel ? $selectedAnswerModel->is_correct : false;

                    if ($isCorrect) {
                        $categorySummary[$categoryId]['benar']++;
                    } else {
                        $categorySummary[$categoryId]['salah']++;
                    }
                    $totalScore += $pointsEarned;
                    $categorySummary[$categoryId]['skor_kategori'] += $pointsEarned;

                } else {
                    // --- KASUS 2: TIDAK DIJAWAB ---
                    $categorySummary[$categoryId]['kosong']++;
                }
            }
            // --- Akhir Logika Kalkulasi ---

            // 3. Simpan Skor Total ke tabel Ranking
            Ranking::updateOrCreate(
                [
                    'id_user'   => $userTryout->id_user,
                    'tryout_id' => $userTryout->tryout_id,
                ],
                [
                    'score'     => $totalScore 
                ]
            );

            // 4. Simpan Skor Detail per Kategori ke tabel Rapor
            foreach ($categorySummary as $stat) {
                TryoutCategoryScore::updateOrCreate(
                    [
                        'user_tryout_id' => $userTryout->id,
                        'question_category_id' => $stat['category_id'],
                    ],
                    [
                        'score' => $stat['skor_kategori'],
                        'correct_count' => $stat['benar'],
                        'wrong_count' => $stat['salah'],
                        'unanswered_count' => $stat['kosong'],
                        'total_questions' => $stat['total_soal']
                    ]
                );
            }
        }
    }
    
    // =========================================================
    // CSS STATUS NAVIGASI
    // =========================================================
    public function getQuestionStatusClass($index): string
    {
        if (!isset($this->questionIds[$index])) {
            return 'bg-gray-400'; 
        }
        
        $questionId = $this->questionIds[$index];
        
        $doubtfulColor = 'bg-ragu-ragu'; 
        $answeredColor = 'bg-answered'; 
        $unansweredColor = 'bg-red-600';
        
        if ($this->currentQuestionIndex === $index) {
            if ($this->isDoubtful) {
                return $doubtfulColor; 
            }
            if ($this->selectedAnswerId !== null) {
                return $answeredColor; 
            }
        }

        $status = $this->userAnswers[$questionId] ?? null;
        if ($status && $status['is_doubtful']) {
            return $doubtfulColor; 
        }
        if ($status && $status['answer_id'] !== null) {
            return $answeredColor; 
        }

        return $unansweredColor; 
    }

    public function render()
    {
        return view('livewire.customers.tryout-worksheet')
                    ->layout('layouts.blank', ['title' => $this->title]);
    }
}