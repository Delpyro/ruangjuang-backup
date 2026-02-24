<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Ranking;
use App\Models\TryoutCategoryScore;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

/**
 * Controller Worksheet dioptimasi untuk performa maksimal.
 * Target response time: < 500ms.
 */
class TryoutWorksheet extends Component
{
    public Tryout $tryout;
    public $userTryoutId; 
    public $endTime;
    public $title;

    // State ringan untuk meminimalisir payload JSON Livewire
    public int $currentIndex = 0; 
    public array $questionIds = []; 
    public array $userAnswers = []; 
    public int $totalQuestions = 0;
    public float $progressPercent = 0;

    public function mount(Tryout $tryout, $attempt = 1)
    {
        $this->tryout = $tryout;
        $this->title = 'Pengerjaan: ' . $this->tryout->title;

        $userTryout = UserTryout::where('id_user', Auth::id())
            ->where('tryout_id', $this->tryout->id)
            ->where('attempt', $attempt)
            ->first();

        if (!$userTryout || $userTryout->is_completed) {
            return $this->redirect(route('tryout.my-tryouts'), navigate: true);
        }

        if (Carbon::now()->isAfter($userTryout->ended_at)) {
            $this->forceFinishExam($userTryout);
            return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
        }

        $this->userTryoutId = $userTryout->id;
        $this->endTime = $userTryout->ended_at->toIso8601String();

        // Ambil ID soal dengan urutan tetap untuk efisiensi fetch
        $this->questionIds = $this->tryout->activeQuestions()
            ->orderBy('id', 'asc')
            ->pluck('id')
                ->toArray();
        
        $this->totalQuestions = count($this->questionIds);

        $this->loadUserAnswers();
        $this->calculateProgress();
    }

    /**
     * OPTIMASI 1: Fetch-on-Demand dengan Select Kolom Terbatas.
     * Mengurangi penggunaan memori server saat merender soal.
     */
    #[Computed]
    public function currentQuestion()
    {
        if (!isset($this->questionIds[$this->currentIndex])) return null;

        return Question::with(['answers' => function($query) {
                $query->select('id', 'id_question', 'answer'); 
            }])
            ->select('id', 'id_question_categories', 'id_question_sub_category', 'question', 'image')
            ->find($this->questionIds[$this->currentIndex]);
    }

    private function loadUserAnswers()
    {
        $this->userAnswers = UserAnswer::where('user_tryout_id', $this->userTryoutId)
            ->get(['question_id', 'answer_id', 'is_doubtful'])
            ->keyBy('question_id')
            ->map(fn($item) => [
                'answer_id' => $item->answer_id,
                'is_doubtful' => (bool)$item->is_doubtful,
            ])->toArray();
    }

    /**
     * OPTIMASI 2: Menggunakan Query Builder (DB Table).
     * Bypass Eloquent Model untuk performa simpan yang jauh lebih cepat.
     */
    public function saveAnswer($questionId, $answerId, $isDoubtful = false)
    {
        try {
            if (!$this->userTryoutId || !$questionId) return;

            // Ambil poin langsung via Query Builder (lebih cepat dari Eloquent)
            $points = 0;
            if ($answerId) {
                $points = DB::table('answers')->where('id', $answerId)->value('points') ?? 0;
            }

            // Database Persistence menggunakan updateOrInsert (High Speed)
            DB::table('users_answers')->updateOrInsert(
                [
                    'user_tryout_id' => $this->userTryoutId, 
                    'question_id'    => $questionId
                ],
                [
                    'id_user'     => Auth::id(),
                    'answer_id'   => $answerId,
                    'is_doubtful' => $isDoubtful,
                    'score'       => $points,
                    'updated_at'  => now()
                ]
            );

            // Update state lokal untuk visual sidebar & progress
            $this->userAnswers[$questionId] = [
                'answer_id' => $answerId,
                'is_doubtful' => $isDoubtful,
            ];

            $this->calculateProgress();

        } catch (\Exception $e) {
            logger()->error("Koneksi Database Lambat/Gagal: " . $e->getMessage());
        }
    }

    private function calculateProgress()
    {
        $answeredCount = collect($this->userAnswers)->whereNotNull('answer_id')->count();
        $this->progressPercent = $this->totalQuestions > 0 
            ? round(($answeredCount / $this->totalQuestions) * 100, 1) 
            : 0;
    }

    public function finishExam()
    {
        $userTryout = UserTryout::find($this->userTryoutId);
        $this->forceFinishExam($userTryout);
        
        session()->flash('success', 'Ujian telah berhasil dikumpulkan.');
        return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
    }

    /**
     * Finalisasi Skor (Sesuai kebutuhan Bank NTT).
     */
    private function forceFinishExam(UserTryout $userTryout)
    {
        DB::transaction(function () use ($userTryout) {
            $userTryout->update([
                'is_completed' => true,
                'ended_at'     => Carbon::now()
            ]);

            if ($userTryout->attempt == 1) {
                // Kalkulasi akhir tetap menggunakan Eloquent karena dijalankan hanya sekali
                $allQuestions = $this->tryout->activeQuestions()->with('category')->get();
                $savedAnswers = UserAnswer::where('user_tryout_id', $userTryout->id)->get()->keyBy('question_id');

                $totalScore = 0;
                $summary = [];

                foreach ($allQuestions as $q) {
                    $catId = $q->id_question_categories ?? 0;
                    if (!isset($summary[$catId])) {
                        $summary[$catId] = ['score' => 0, 'correct' => 0, 'wrong' => 0, 'empty' => 0, 'total' => 0];
                    }

                    $summary[$catId]['total']++;
                    $ans = $savedAnswers->get($q->id);

                    if ($ans && $ans->answer_id) {
                        $totalScore += $ans->score;
                        $summary[$catId]['score'] += $ans->score;

                        $isCorrect = DB::table('answers')->where('id', $ans->answer_id)->value('is_correct');
                        $isCorrect ? $summary[$catId]['correct']++ : $summary[$catId]['wrong']++;
                    } else {
                        $summary[$catId]['empty']++;
                    }
                }

                Ranking::updateOrCreate(
                    ['id_user' => Auth::id(), 'tryout_id' => $userTryout->tryout_id],
                    ['score' => $totalScore]
                );

                foreach ($summary as $catId => $stat) {
                    TryoutCategoryScore::updateOrCreate(
                        ['user_tryout_id' => $userTryout->id, 'question_category_id' => $catId],
                        [
                            'score'            => $stat['score'],
                            'correct_count'    => $stat['correct'],
                            'wrong_count'      => $stat['wrong'],
                            'unanswered_count' => $stat['empty'],
                            'total_questions'  => $stat['total']
                        ]
                    );
                }
            }
        });
    }

    public function render()
    {
        return view('livewire.customers.tryout-worksheet')
            ->layout('layouts.blank');
    }
}