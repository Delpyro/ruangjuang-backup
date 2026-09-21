<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class TryoutDiscussionPage extends Component
{
    public Tryout $tryout;
    public ?UserTryout $userTryout = null;
    
    // Properti Soal (Hanya ID, untuk menghindari hydration error)
    public array $questionIds = []; 
    public ?Question $currentQuestion = null; 
    public int $currentQuestionIndex = 0; 
    public int $totalQuestions = 0;
    
    // Properti Jawaban Pengguna (untuk ditampilkan)
    // Format: [ question_id => ['answer_id' => X, 'is_doubtful' => Y] ]
    public array $userAnswers = []; 

    /**
     * Mount komponen, validasi, dan inisialisasi data.
     */
    public function mount(Tryout $tryout)
    {
        $this->tryout = $tryout;

        // 1. Dapatkan sesi pengerjaan yang sudah selesai (completed)
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
        
        $this->loadUserProgress();
        $this->setCurrentQuestion();
    }

    /**
     * Helper: Memuat semua soal dan menyimpan hanya ID-nya.
     */
    private function loadQuestions(): bool
    {
        // Ambil semua soal aktif dari tryout
        $questionModels = $this->tryout->activeQuestions() 
                                       ->select('id', 'id_question_categories')
                                       // Anda bisa menambahkan order/sort jika perlu
                                       ->get();
        
        $this->questionIds = $questionModels->pluck('id')->toArray();
        $this->totalQuestions = count($this->questionIds);

        if ($this->totalQuestions == 0) {
            session()->flash('error', 'Tryout ini belum memiliki soal aktif.');
            $this->redirect(route('tryout.my-results', $this->tryout->slug));
            return false;
        }
        
        return true;
    }

    /**
     * Helper: Memuat jawaban user yang sudah tersimpan untuk sesi ini (UserTryout).
     */
    private function loadUserProgress()
    {
        // Ambil semua jawaban untuk sesi tryout ini
        $answers = UserAnswer::where('user_tryout_id', $this->userTryout->id) 
                            ->whereIn('question_id', $this->questionIds)
                            ->get();
        
        // Ubah ke array PHP biasa agar aman dari hydration error
        $this->userAnswers = $answers->keyBy('question_id')->map(function ($answer) {
            return [
                'answer_id' => $answer->answer_id,
                'is_doubtful' => $answer->is_doubtful,
            ];
        })->toArray();
    }

    /**
     * Helper: Mengatur soal saat ini berdasarkan index dan me-load model dari DB.
     */
    private function setCurrentQuestion()
    {
        // Pastikan index valid
        if ($this->currentQuestionIndex < 0) {
            $this->currentQuestionIndex = 0;
        }
        if ($this->currentQuestionIndex >= $this->totalQuestions) {
            $this->currentQuestionIndex = $this->totalQuestions - 1;
        }

        $questionId = $this->questionIds[$this->currentQuestionIndex] ?? null;

        if (!$questionId) {
            return;
        }

        // RE-FETCH Model dengan semua relasi yang dibutuhkan untuk tampilan pembahasan
        $this->currentQuestion = Question::with(['answers', 'correctAnswer', 'category'])->find($questionId);

        if (! $this->currentQuestion) {
            session()->flash('error', 'Gagal memuat soal pembahasan.');
        }
    }

    // ----------------------------------------------------------------------
    // NAVIGATION METHODS (Tidak ada saveAnswer() karena ini hanya tampilan)
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
        $this->currentQuestionIndex = $index;
        $this->setCurrentQuestion();
    }
    
    /**
     * Helper untuk styling nomor soal di sidebar
     */
    public function getQuestionStatusClass($index): string
    {
        if (!isset($this->questionIds[$index])) {
            return 'bg-gray-400';
        }
        
        $questionId = $this->questionIds[$index];

        if ($this->currentQuestionIndex === $index) {
            return 'bg-green-500 border-2 border-green-700'; // Soal Saat Ini
        }

        $status = $this->userAnswers[$questionId] ?? null;
        $isAnswered = $status && $status['answer_id'] !== null;
        $isDoubtful = $status && $status['is_doubtful'];
        
        // Tentukan status berdasarkan Jawaban Pengguna
        if ($isDoubtful) {
            return 'bg-yellow-500'; // Ragu-ragu
        }
        if ($isAnswered) {
            return 'bg-blue-600'; // Terjawab
        }

        return 'bg-red-600'; // Belum dijawab
    }

    public function render()
    {
        return view('livewire.customers.tryout-discussion-worksheet')
                    ->layout('layouts.blank');
    }
}