<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class TryoutExecutionPage extends Component
{
    // State Tryout
    public Tryout $tryout;
    public UserTryout $userTryout; // Baris pivot spesifik (sesi pengerjaan)
    public $questions; // Collection of Question Models (diambil di mount)

    // State Pengerjaan
    public $currentQuestionIndex = 0;
    // Format: [question_id => ['answer_id' => X, 'is_doubtful' => Y]]
    public $userAnswers = []; 

    // State Timer/Status
    public $endTime; // Waktu berakhir sesi (milliseconds)
    public $isExpired = false;
    public $timerKey; // Key untuk sinkronisasi Alpine.js

    protected $listeners = ['forceSubmit' => 'submitTryout'];

    // Dependency Injection di mount untuk mendapatkan Tryout Model
    public function mount(Tryout $tryout, $attempt)
    {
        $user = Auth::user();
        $this->tryout = $tryout;

        try {
            // 1. Verifikasi UserTryout (Sesi Pengerjaan)
            $userTryout = UserTryout::where('id_user', $user->id)
                                    ->where('tryout_id', $tryout->id)
                                    ->where('attempt', $attempt)
                                    ->firstOrFail();

            $this->userTryout = $userTryout;

            // 2. Cek Status Sesi
            if ($userTryout->is_completed) {
                // Jika sudah selesai, redirect ke halaman hasil
                return $this->redirect(route('tryout.my-results', $tryout->slug));
            }
            if (!$userTryout->started_at) {
                // Jika belum dimulai (kemungkinan user bypass halaman konfirmasi)
                session()->flash('error', 'Sesi belum dimulai. Mohon mulai dari halaman "Tryout Saya".');
                return $this->redirect(route('my-tryouts'));
            }

            // 3. Hitung Waktu Habis & Set State Timer
            // Menggunakan milidetik untuk sinkronisasi Alpine.js
            $this->endTime = $userTryout->ended_at->timestamp * 1000; 
            $this->timerKey = 'tryout_timer_' . $userTryout->id;

            if ($userTryout->ended_at->isPast()) {
                $this->isExpired = true;
                $this->submitTryout(true); // Submit paksa jika waktu habis
            }

            // 4. Ambil Soal dan Jawaban User
            $this->questions = $tryout->questions()->active()->with('answers')->get();

            $savedAnswers = UserAnswer::where('user_tryout_id', $this->userTryout->id)
                                      ->get()
                                      ->keyBy('question_id');

            foreach ($this->questions as $question) {
                $saved = $savedAnswers->get($question->id);
                $this->userAnswers[$question->id] = [
                    'answer_id' => $saved->answer_id ?? null,
                    'is_doubtful' => $saved->is_doubtful ?? false,
                ];
            }
            
            // Atur index pertanyaan pertama yang belum terjawab
            $this->setInitialQuestionIndex();

        } catch (Exception $e) {
            \Log::error("Tryout Execution Mount Failed: " . $e->getMessage());
            session()->flash('error', 'Sesi Tryout tidak valid atau telah berakhir.');
            return $this->redirect(route('my-tryouts'));
        }
    }
    
    /**
     * Menetapkan index pertanyaan pertama yang belum terjawab saat mount.
     */
    private function setInitialQuestionIndex()
    {
        foreach ($this->questions as $index => $question) {
            $answer = $this->userAnswers[$question->id]['answer_id'] ?? null;
            if (is_null($answer)) {
                $this->currentQuestionIndex = $index;
                return;
            }
        }
        // Jika semua terjawab, kembali ke soal pertama.
        $this->currentQuestionIndex = 0;
    }

    /**
     * Simpan jawaban (dipanggil ketika radio button berubah)
     */
    public function saveAnswer($questionId, $answerId)
    {
        if ($this->isExpired || $this->userTryout->is_completed) return;

        $questionId = (int) $questionId;
        $answerId = (int) $answerId;

        $this->userAnswers[$questionId]['answer_id'] = $answerId;
        $this->userAnswers[$questionId]['is_doubtful'] = false;

        // Simpan ke database
        UserAnswer::updateOrCreate(
            [
                'user_tryout_id' => $this->userTryout->id,
                'question_id' => $questionId,
            ],
            [
                'id_user' => Auth::id(),
                'answer_id' => $answerId,
                'is_doubtful' => false,
                'score' => 0, // Score akan dihitung saat submit
            ]
        );
        $this->dispatch('answer-saved', ['questionId' => $questionId]);
    }

    /**
     * Toggle status ragu-ragu
     */
    public function toggleDoubtful($questionId)
    {
        if ($this->isExpired || $this->userTryout->is_completed) return;

        $questionId = (int) $questionId;
        
        // Pastikan array pertanyaan ada
        if (!isset($this->userAnswers[$questionId])) {
            $this->userAnswers[$questionId] = ['answer_id' => null, 'is_doubtful' => false];
        }
        
        $isDoubtful = !$this->userAnswers[$questionId]['is_doubtful'];
        $this->userAnswers[$questionId]['is_doubtful'] = $isDoubtful;

        // Simpan/update ke database
        UserAnswer::updateOrCreate(
            [
                'user_tryout_id' => $this->userTryout->id,
                'question_id' => $questionId,
            ],
            [
                'id_user' => Auth::id(),
                'answer_id' => $this->userAnswers[$questionId]['answer_id'],
                'is_doubtful' => $isDoubtful,
                'score' => 0, 
            ]
        );
        $this->dispatch('doubtful-toggled', ['questionId' => $questionId, 'isDoubtful' => $isDoubtful]);
    }

    /**
     * Navigasi pertanyaan
     */
    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < $this->questions->count()) {
            $this->currentQuestionIndex = $index;
            // Gunakan Alpine untuk scroll ke atas (jika di mobile)
            $this->dispatch('scroll-to-top');
        }
    }

    /**
     * Finalisasi Tryout dan Hitung Skor
     */
    public function submitTryout(bool $isForced = false)
    {
        if ($this->userTryout->is_completed) {
            session()->flash('info', 'Tryout sudah selesai sebelumnya.');
            return $this->redirect(route('tryout.my-results', $this->tryout->slug));
        }

        try {
            DB::transaction(function () use ($isForced) {
                
                // 1. Ambil semua jawaban user untuk sesi ini
                $answers = UserAnswer::with('question.correctAnswer')
                                     ->where('user_tryout_id', $this->userTryout->id)
                                     ->get();

                $totalScore = 0;
                $answeredCount = 0;
                $correctCount = 0;
                $incorrectCount = 0;

                foreach ($answers as $userAnswer) {
                    $score = 0;
                    
                    if ($userAnswer->answer_id) {
                        $answeredCount++;
                        
                        $correctAnswer = $userAnswer->question->correctAnswer;
                        $isCorrect = $correctAnswer && ($userAnswer->answer_id === $correctAnswer->id);
                        
                        if ($isCorrect) {
                            $score = 4; // Skor benar +4
                            $correctCount++;
                        } else {
                            $score = -1; // Skor salah -1
                            $incorrectCount++;
                        }

                        $totalScore += $score;
                        
                        // Update skor di tabel users_answers
                        $userAnswer->score = $score;
                        $userAnswer->save();
                    }
                }
                
                $totalQuestions = $this->questions->count();
                $unansweredCount = $totalQuestions - $answeredCount;

                // 2. Finalisasi Sesi di UserTryout
                $this->userTryout->update([
                    'ended_at' => Carbon::now(), 
                    'is_completed' => true,
                    'metadata' => array_merge(($this->userTryout->metadata ?? []), [
                        'final_score' => $totalScore,
                        'correct_count' => $correctCount,
                        'incorrect_count' => $incorrectCount,
                        'unanswered_count' => $unansweredCount,
                        'is_forced_submit' => $isForced,
                        'submission_time' => Carbon::now()->toIso8601String()
                    ])
                ]);
            });

            // 3. Redirect ke halaman hasil
            session()->flash('success', 'Tryout berhasil diselesaikan!');
            
            // Hapus timer dari localStorage setelah berhasil submit
            $this->dispatch('remove-timer', ['key' => $this->timerKey]);

            return $this->redirect(route('tryout.my-results', $this->tryout->slug));

        } catch (\Throwable $e) {
            \Log::error("Tryout Submission Failed for UserTryout ID {$this->userTryout->id}: " . $e->getMessage());
            
            session()->flash('error', 'Gagal menyelesaikan tryout: Terjadi kesalahan server.');
            $this->dispatch('show-toast', ['message' => 'Gagal submit: Terjadi kesalahan.', 'type' => 'error']);
        }
    }

    public function render()
    {
        // Logika render tetap ringkas
        $currentQuestion = $this->questions[$this->currentQuestionIndex] ?? null;

        $answeredCount = collect($this->userAnswers)->whereNotNull('answer_id')->count();
        $doubtfulCount = collect($this->userAnswers)->where('is_doubtful', true)->count();
        $totalQuestions = $this->questions->count();
        $unansweredCount = $totalQuestions - $answeredCount;
        
        $stats = compact('answeredCount', 'doubtfulCount', 'unansweredCount', 'totalQuestions');


        return view('livewire.customers.tryout-execution-page', compact('currentQuestion', 'stats'))
            ->layout('layouts.app');
    }
}
