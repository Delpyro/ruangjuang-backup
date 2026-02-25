<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Ranking;
use App\Models\Question;
use App\Models\TryoutCategoryScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class TryoutWorksheet extends Component
{
    public Tryout $tryout;
    public $userTryout;
    public $endTime;
    public $title;

    // --- State Minimalis (Hanya ID & Index) ---
    public array $questionIds = []; 
    public int $currentIndex = 0;
    public int $totalQuestions = 0;

    // --- State untuk UI Sidebar (Sangat Ringan) ---
    // Format: [question_id => ['answered' => true, 'is_doubtful' => false]]
    public array $questionStatus = []; 
    
    public float $progressPercent = 0;

    public function mount(Tryout $tryout, $attempt = 1)
    {
        $this->tryout = $tryout;
        $this->title = 'Pengerjaan: ' . $this->tryout->title;

        $userTryout = UserTryout::where('id_user', Auth::id())
            ->where('tryout_id', $this->tryout->id)
            ->where('attempt', $attempt)
            ->first();

        // Proteksi Akses
        if (!$userTryout || $userTryout->is_completed) {
            return $this->redirect(route('tryout.my-tryouts'), navigate: true);
        }

        // Proteksi Waktu
        if (Carbon::now()->isAfter($userTryout->ended_at)) {
            $this->forceFinishExam($userTryout);
            return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
        }

        $this->userTryout = $userTryout;
        $this->endTime = $userTryout->ended_at->toIso8601String();

        $this->initWorksheet();
    }

    /**
     * Inisialisasi ID soal dan status jawaban.
     */
    private function initWorksheet()
    {
        // 1. Ambil hanya ID soal (Order sesuai urutan tryout)
        $this->questionIds = $this->tryout->activeQuestions()
            ->orderBy('id', 'asc')
            ->pluck('id')
            ->toArray();

        $this->totalQuestions = count($this->questionIds);

        // 2. Ambil status jawaban untuk sidebar (Hanya ID dan flag)
        $savedAnswers = UserAnswer::where('user_tryout_id', $this->userTryout->id)
            ->get(['question_id', 'answer_id', 'is_doubtful']);

        foreach ($savedAnswers as $ans) {
            $this->questionStatus[$ans->question_id] = [
                'answered' => !is_null($ans->answer_id),
                'is_doubtful' => (bool)$ans->is_doubtful,
                'selected_id' => $ans->answer_id
            ];
        }

        $this->calculateProgress();
    }

    /**
     * Lazy Loading: Mengambil detail soal secara dinamis.
     * Menggunakan Cache agar perpindahan nomor soal instan (0ms).
     */
    #[Computed]
    public function currentQuestion()
    {
        $id = $this->questionIds[$this->currentIndex] ?? null;
        if (!$id) return null;

        // Cache soal selama 1 jam (karena isi soal jarang berubah saat ujian)
        return Cache::remember("question_detail_{$id}", 3600, function () use ($id) {
            return Question::with(['subCategory', 'answers' => function($query) {
                $query->select('id', 'id_question', 'answer'); 
            }])->find($id, ['id', 'question', 'image', 'id_question_sub_category']);
        });
    }

    /**
     * Navigasi Soal (Hanya merubah Index, bukan reload data berat)
     */
    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < $this->totalQuestions) {
            $this->currentIndex = $index;
        }
    }

    /**
     * Simpan Jawaban
     */
    public function saveAnswer($answerId, $isDoubtful = false)
    {
        $questionId = $this->questionIds[$this->currentIndex];

        // Hitung poin di server (Security)
        $points = 0;
        if ($answerId) {
            $points = DB::table('answers')->where('id', $answerId)->value('points') ?? 0;
        }

        UserAnswer::updateOrCreate(
            ['user_tryout_id' => $this->userTryout->id, 'question_id' => $questionId],
            [
                'id_user' => Auth::id(),
                'answer_id' => $answerId,
                'is_doubtful' => $isDoubtful,
                'score' => $points
            ]
        );

        // Update UI status lokal (Sidebar)
        $this->questionStatus[$questionId] = [
            'answered' => !is_null($answerId),
            'is_doubtful' => $isDoubtful,
            'selected_id' => $answerId
        ];

        $this->calculateProgress();

        // Auto-next ke soal berikutnya jika bukan soal terakhir
        if ($this->currentIndex < $this->totalQuestions - 1) {
            $this->currentIndex++;
        }
    }

    private function calculateProgress()
    {
        $answeredCount = collect($this->questionStatus)->where('answered', true)->count();
        $this->progressPercent = $this->totalQuestions > 0 
            ? round(($answeredCount / $this->totalQuestions) * 100, 1) 
            : 0;
    }

    public function finishExam()
    {
        $this->forceFinishExam($this->userTryout);
        session()->flash('success', 'Ujian telah berhasil dikumpulkan.');
        return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
    }

    private function forceFinishExam(UserTryout $userTryout)
    {
        DB::transaction(function () use ($userTryout) {
            $userTryout->update([
                'is_completed' => true,
                'ended_at' => Carbon::now()
            ]);

            if ($userTryout->attempt == 1) {
                $savedAnswers = UserAnswer::where('user_tryout_id', $userTryout->id)->get();
                $totalScore = $savedAnswers->sum('score');

                // 1. Simpan Ranking
                Ranking::updateOrCreate(
                    ['id_user' => Auth::id(), 'tryout_id' => $userTryout->tryout_id],
                    ['score' => $totalScore]
                );

                // 2. Kalkulasi Rapor per Kategori (Optimized with GroupBy)
                $answersByQuestion = $savedAnswers->keyBy('question_id');
                $questions = $this->tryout->activeQuestions()->get(['id', 'id_question_categories']);
                
                $summary = [];
                foreach ($questions as $q) {
                    $catId = $q->id_question_categories;
                    $ans = $answersByQuestion->get($q->id);
                    
                    if (!isset($summary[$catId])) {
                        $summary[$catId] = ['score' => 0, 'correct' => 0, 'wrong' => 0, 'empty' => 0, 'total' => 0];
                    }

                    $summary[$catId]['total']++;
                    if ($ans && $ans->answer_id) {
                        $summary[$catId]['score'] += $ans->score;
                        // Logika is_correct bisa ditambah jika field tersedia di tabel answers
                    } else {
                        $summary[$catId]['unanswered']++;
                    }
                }

                foreach ($summary as $catId => $stat) {
                    TryoutCategoryScore::updateOrCreate(
                        ['user_tryout_id' => $userTryout->id, 'question_category_id' => $catId],
                        [
                            'score' => $stat['score'],
                            'total_questions' => $stat['total'],
                        ]
                    );
                }
            }
        });
    }

    public function render()
    {
        return view('livewire.customers.tryout-worksheet', [
            // Pastikan menggunakan camelCase sesuai nama fungsi #[Computed]
            'currentQuestion' => $this->currentQuestion 
        ])->layout('layouts.blank');
    }
}