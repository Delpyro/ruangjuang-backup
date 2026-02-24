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

class TryoutWorksheet extends Component
{
    public Tryout $tryout;
    public $userTryoutId; 
    public $endTime;
    public $title;

    /** * STATE RINGAN
     * Hanya menyimpan ID dan Index agar payload tetap kecil (< 10KB).
     */
    public int $currentIndex = 0; 
    public array $questionIds = []; 
    public array $userAnswers = []; 
    public int $totalQuestions = 0;
    public float $progressPercent = 0;

    /**
     * Inisialisasi awal saat halaman dimuat.
     */
    public function mount(Tryout $tryout, $attempt = 1)
    {
        $this->tryout = $tryout;
        $this->title = 'Pengerjaan: ' . $this->tryout->title;

        $userTryout = UserTryout::where('id_user', Auth::id())
            ->where('tryout_id', $this->tryout->id)
            ->where('attempt', $attempt)
            ->first();

        // Proteksi akses dan waktu
        if (!$userTryout || $userTryout->is_completed) {
            return $this->redirect(route('tryout.my-tryouts'), navigate: true);
        }

        if (Carbon::now()->isAfter($userTryout->ended_at)) {
            $this->forceFinishExam($userTryout);
            return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
        }

        $this->userTryoutId = $userTryout->id;
        $this->endTime = $userTryout->ended_at->toIso8601String();

        // Strategi Pagination: Hanya ambil ID Soal
        $this->questionIds = $this->tryout->activeQuestions()
            ->orderBy('id', 'asc')
            ->pluck('id')
            ->toArray();
        
        $this->totalQuestions = count($this->questionIds);

        $this->loadUserAnswers();
        $this->calculateProgress();
    }

    /**
     * COMPUTED PROPERTY: Fetch-on-Demand.
     * Mengambil detail soal hanya saat index berubah. 
     * Data ini tidak dikirim bolak-balik dalam state JSON Livewire.
     */
    #[Computed]
    public function currentQuestion()
    {
        if (!isset($this->questionIds[$this->currentIndex])) return null;

        return Question::with(['subCategory', 'answers' => function($query) {
                $query->select('id', 'id_question', 'answer'); 
            }])
            ->select('id', 'id_question_categories', 'id_question_sub_category', 'question', 'image')
            ->find($this->questionIds[$this->currentIndex]);
    }

    /**
     * Memuat data jawaban tersimpan untuk sinkronisasi UI.
     */
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
     * Menyimpan jawaban. 
     * Diperbaiki agar tetap menyimpan status ragu meski jawaban kosong.
     */
    public function saveAnswer($questionId, $answerId, $isDoubtful = false)
    {
        try {
            if (!$this->userTryoutId || !$questionId) return;

            $points = 0;
            if ($answerId) {
                $points = DB::table('answers')->where('id', $answerId)->value('points') ?? 0;
            }

            UserAnswer::updateOrCreate(
                [
                    'user_tryout_id' => $this->userTryoutId, 
                    'question_id'    => $questionId
                ],
                [
                    'id_user'     => Auth::id(),
                    'answer_id'   => $answerId,
                    'is_doubtful' => $isDoubtful,
                    'score'       => $points
                ]
            );

            // Update state lokal untuk warna sidebar instan
            $this->userAnswers[$questionId] = [
                'answer_id' => $answerId,
                'is_doubtful' => $isDoubtful,
            ];

            $this->calculateProgress();

        } catch (\Exception $e) {
            logger()->error("Gagal simpan jawaban User: " . Auth::id() . " | Error: " . $e->getMessage());
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
        
        session()->flash('success', 'Ujian telah dikumpulkan.');
        return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
    }

    /**
     * Finalisasi Kalkulasi Skor.
     * Menangani skor per kategori sesuai kebutuhan proyek Bank NTT.
     */
    private function forceFinishExam(UserTryout $userTryout)
    {
        DB::transaction(function () use ($userTryout) {
            $userTryout->update([
                'is_completed' => true,
                'ended_at'     => Carbon::now()
            ]);

            if ($userTryout->attempt == 1) {
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