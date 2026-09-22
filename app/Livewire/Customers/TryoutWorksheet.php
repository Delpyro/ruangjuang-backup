<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Ranking;
use App\Models\TryoutCategoryScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Carbon\Carbon;

class TryoutWorksheet extends Component
{
    #[Locked]
    public int $tryoutId;

    #[Locked]
    public int $userTryoutId;

    #[Locked]
    public int $userId;

    

    /** Timestamp server saat mount (ms) - untuk koreksi clock skew */
    public int $serverTimestamp = 0;
    public string $endedAt = '';
    public string $title = '';
    public string $tryoutSlug = '';

    // =========================================================================
    // MOUNT
    // =========================================================================

    public function mount(Tryout $tryout, int $attempt = 1): void
    {
        $this->tryoutId = $tryout->id;
        $this->title   = $tryout->title;
        $this->tryoutSlug = $tryout->slug;
        $this->userId  = Auth::id();

        $userTryout = UserTryout::where('id_user', $this->userId)
            ->where('tryout_id', $tryout->id)
            ->where('attempt', $attempt)
            ->first();

        if (!$userTryout || $userTryout->is_completed || !$userTryout->ended_at) {
            $this->redirectRoute('tryout.my-tryouts', navigate: true);
            return;
        }

        if (Carbon::now()->isAfter($userTryout->ended_at)) {
            $this->forceFinishExam($userTryout);
            $this->redirectRoute('tryout.my-results', [$tryout->slug], navigate: true);
            return;
        }

        $this->userTryoutId    = $userTryout->id;
        $this->endedAt         = $userTryout->ended_at->toIso8601String();
        $this->serverTimestamp = (int)(microtime(true) * 1000);

    }

    // =========================================================================
    // AUTO-SAVE
    // =========================================================================

    #[Renderless]
    public function autoSave(array $payload): bool
    {
        if (empty($payload)) {
            return true;
        }

        $userTryout = UserTryout::select('ended_at', 'is_completed')
            ->where('id', $this->userTryoutId)
            ->where('id_user', $this->userId)
            ->first();
        if (!$userTryout || $userTryout->is_completed) {
            return false;
        }
        if (Carbon::now()->isAfter(Carbon::parse($userTryout->ended_at)->addMinutes(2))) {
            return false;
        }

        $payload = array_slice($payload, 0, 300);

        // [FIX #F] Whitelist dari DB, bukan dari payload (menghemat 500KB bandwidth)
        $tryoutId = \App\Models\UserTryout::where('id', $this->userTryoutId)->value('tryout_id');
        $validQuestionIds = \Illuminate\Support\Facades\DB::table('questions')
            ->where('id_tryout', $tryoutId)
            ->where('is_active', true)
            ->pluck('id')
            ->flip()
            ->all();

        $answerIds = collect($payload)
            ->pluck('answerId')
            ->filter(fn ($id) => is_numeric($id) && $id > 0)
            ->unique()->values()->all();

        $answersData = empty($answerIds)
            ? []
            : DB::table('answers')->whereIn('id', $answerIds)->get(['id', 'id_question', 'points'])->keyBy('id')->all();

        $now = now();

        $records = collect($payload)
            ->filter(fn ($item) => isset($item['questionId']) && is_numeric($item['questionId']) && isset($validQuestionIds[(int)$item['questionId']]))
            ->map(function ($item) use ($answersData, $now) {
                $qId = (int) $item['questionId'];
                $aId = (isset($item['answerId']) && is_numeric($item['answerId']) && $item['answerId'] > 0) ? (int)$item['answerId'] : null;
                
                // Pastikan answer_id ada dan benar-benar milik question_id ini
                $ans = $aId && isset($answersData[$aId]) ? $answersData[$aId] : null;
                $validAnswerId = ($ans && $ans->id_question == $qId) ? $ans->id : null;
                $score = $validAnswerId ? (float) $ans->points : 0;
                
                return [
                    'user_tryout_id' => $this->userTryoutId,
                    'id_user'        => $this->userId,
                    'question_id'    => $qId,
                    'answer_id'      => $validAnswerId,
                    'is_doubtful'    => (bool) ($item['isDoubtful'] ?? false),
                    'score'          => $score,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            })
            ->values()->all();

        if (empty($records)) {
            return true;
        }

        UserAnswer::upsert(
            $records,
            ['user_tryout_id', 'question_id'],
            ['answer_id', 'is_doubtful', 'score', 'updated_at']
        );

        return true;
    }

    // =========================================================================
    // FINISH EXAM
    // =========================================================================

    public function finishExam(): void
    {
        $userTryout = UserTryout::where('id', $this->userTryoutId)
            ->where('id_user', $this->userId)
            ->first();

        if (!$userTryout) {
            $this->redirectRoute('tryout.my-tryouts', navigate: true);
            return;
        }

        if ($userTryout->is_completed) {
            $this->redirectRoute('tryout.my-results', [$this->tryoutSlug], navigate: true);
            return;
        }

        $this->forceFinishExam($userTryout);
        $this->redirectRoute('tryout.my-results', [$this->tryoutSlug], navigate: true);
    }

    private function forceFinishExam(UserTryout $userTryout): void
    {
        DB::transaction(function () use ($userTryout) {

            $updates = ['is_completed' => true];
            if (!$userTryout->ended_at) {
                $updates['ended_at'] = Carbon::now();
            }
            $userTryout->update($updates);

            if ($userTryout->attempt !== 1) {
                return;
            }

            $answers    = UserAnswer::where('user_tryout_id', $userTryout->id)->get();
            $totalScore = $answers->sum('score');

            Ranking::updateOrCreate(
                ['id_user' => $userTryout->id_user, 'tryout_id' => $userTryout->tryout_id],
                ['score'   => $totalScore]
            );

            $questions = \App\Models\Tryout::find($userTryout->tryout_id)
                ->questions()
                ->with('subCategory:id,question_category_id')
                ->get(['id', 'id_question_categories', 'id_question_sub_category']);

            $answersByQuestion = $answers->keyBy('question_id');
            $summary           = [];

            foreach ($questions as $q) {
                $catId = $q->subCategory->question_category_id
                    ?? $q->id_question_categories
                    ?? 0;

                if (!isset($summary[$catId])) {
                    $summary[$catId] = ['score' => 0, 'total' => 0, 'correct' => 0, 'wrong' => 0, 'unanswered' => 0];
                }

                $summary[$catId]['total']++;
                $ans = $answersByQuestion->get($q->id);

                if ($ans && $ans->answer_id) {
                    $summary[$catId]['score'] += $ans->score;
                    $ans->score > 0 ? $summary[$catId]['correct']++ : $summary[$catId]['wrong']++;
                } else {
                    $summary[$catId]['unanswered']++;
                }
            }

            $now     = Carbon::now();
            $records = collect($summary)->map(fn ($stat, $catId) => [
                'user_tryout_id'       => $userTryout->id,
                'question_category_id' => $catId,
                'score'                => $stat['score'],
                'total_questions'      => $stat['total'],
                'correct_count'        => $stat['correct'],
                'wrong_count'          => $stat['wrong'],
                'unanswered_count'     => $stat['unanswered'],
                'created_at'           => $now,
                'updated_at'           => $now,
            ])->values()->all();

            if (!empty($records)) {
                TryoutCategoryScore::upsert(
                    $records,
                    ['user_tryout_id', 'question_category_id'],
                    ['score', 'total_questions', 'correct_count', 'wrong_count', 'unanswered_count', 'updated_at']
                );
            }
        });
    }

    // =========================================================================
    // UTILITY
    // =========================================================================

    #[Renderless]
    public function ping(): void {}

    public function render()
    {
        $tryout = \App\Models\Tryout::find($this->tryoutId);
        
        $questions = $tryout->activeQuestions()
            ->with([
                'answers:id,id_question,answer,points',
                'subCategory:id,name,question_category_id',
            ])
            ->orderBy('id', 'asc')
            ->get(['id', 'question', 'image', 'explanation', 'id_question_sub_category']);

        $savedAnswers = \App\Models\UserAnswer::where('user_tryout_id', $this->userTryoutId)
            ->get(['question_id', 'answer_id', 'is_doubtful'])
            ->keyBy('question_id');

        $questionsJson = json_encode($questions->map(function ($q, $qIndex) use ($savedAnswers) {
            $letters = ['A', 'B', 'C', 'D', 'E'];
            return [
                'id'           => $q->id,
                'html'         => $q->question,
                'image'        => $q->image ? asset('storage/' . $q->image) : null,
                'subcategory'  => $q->subCategory?->name ?? 'Soal',
                'answers'      => $q->answers->sortBy('id')->values()->map(function ($a, $i) use ($letters) {
                    return [
                        'id'     => $a->id,
                        'html'   => $a->answer,
                        'letter' => $letters[$i] ?? chr(65 + $i),
                    ];
                })->all(),
                'savedAnswerId' => $savedAnswers->get($q->id)?->answer_id,
                'savedDoubtful' => (bool) ($savedAnswers->get($q->id)?->is_doubtful ?? false),
            ];
        })->all(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        return view('livewire.customers.tryout-worksheet', [
            'questionsJson' => $questionsJson
        ])->layout('layouts.blank');
    }
}
