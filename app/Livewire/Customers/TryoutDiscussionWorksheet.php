<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Question;
use App\Models\Answer; // <-- Tambahan Model Answer
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TryoutDiscussionWorksheet extends Component
{
    public Tryout $tryout;
    public ?UserTryout $userTryout = null;
    public array $questionsData = []; 

    public function mount(Tryout $tryout)
    {
        $this->tryout = $tryout;

        $this->userTryout = UserTryout::where('id_user', Auth::id())
                                      ->where('tryout_id', $this->tryout->id)
                                      ->where('is_completed', true)
                                      ->latest('ended_at') 
                                      ->first();

        if (!$this->userTryout) {
            session()->flash('error', 'Sesi pembahasan tidak tersedia.');
            return $this->redirect(route('tryout.detail', $tryout->slug));
        }
        
        $this->loadAllDataForClient();
    }

    private function loadAllDataForClient()
    {
        $questions = $this->tryout->activeQuestions()
                          ->with(['answers', 'correctAnswer', 'category', 'subCategory'])
                          ->get();
        if ($questions->isEmpty()) {
            return $this->redirect(route('tryout.my-results', $this->tryout->slug));
        }

        $questionIds = $questions->pluck('id')->toArray();
        $userAnswersGrouped = UserAnswer::where('user_tryout_id', $this->userTryout->id)
                                        ->whereIn('question_id', $questionIds)
                                        ->get()
                                        ->groupBy('question_id');

        $optionLetters = ['A', 'B', 'C', 'D', 'E'];

        $this->questionsData = $questions->map(function ($q) use ($userAnswersGrouped, $optionLetters) {
            $history = $this->getQuestionAnswerHistory($q->id, $userAnswersGrouped); 
            $correctAnswerId = $q->correctAnswer->id ?? null;
            $firstAttempt = $history[0] ?? null;

            // Logika Penentu Warna Sidebar (Benar/Salah)
            $isUserCorrect = false;
            if ($firstAttempt && $correctAnswerId) {
                $isUserCorrect = ($firstAttempt['answer_id'] == $correctAnswerId);
            }

            return [
                'id' => $q->id,
                'category_name' => $q->category->name ?? 'Kategori',
                'subcategory_name' => $q->subCategory->name ?? 'Subkategori',
                'question_html' => $q->question,
                'has_image' => $q->hasImage(),
                'image_url' => $q->hasImage() ? asset('storage/' . $q->image) : null,
                'explanation_html' => $q->explanation ?? '',
                
                // Status utama soal untuk sidebar
                'is_correct' => $isUserCorrect,
                'is_answered' => !empty($history),

                'answers' => $q->answers->sortBy('id')->values()->map(function ($a, $index) use ($correctAnswerId, $history, $firstAttempt, $optionLetters) {
                    $isCorrectAnswer = ($correctAnswerId == $a->id);
                    $isChosenInFirst = $firstAttempt && $firstAttempt['answer_id'] == $a->id;
                    
                    $highlightClass = 'border-gray-200';
                    if ($isCorrectAnswer) {
                        $highlightClass = 'bg-green-50 border-green-400';
                        if ($isChosenInFirst) $highlightClass = 'bg-green-100 border-green-600';
                    } elseif ($isChosenInFirst) {
                        $highlightClass = 'bg-red-100 border-red-500'; 
                    }

                    $statusTexts = [];
                    foreach ($history as $attempt) {
                        if ($attempt['answer_id'] == $a->id) {
                            $text = "Dipilih pada Pengerjaan ke-{$attempt['attempt_number']}";
                            $textColor = 'text-blue-600 bg-blue-100';
                            if ($attempt['attempt_number'] == 1) {
                                $textColor = $isCorrectAnswer ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200';
                            }
                            $statusTexts[] = ['text' => $text, 'colorClass' => $textColor];
                        }
                    }

                    return [
                        'id' => $a->id,
                        'letter' => $optionLetters[$index] ?? '',
                        'answer_html' => $a->answer,
                        'highlight_class' => $highlightClass,
                        'status_badges' => $statusTexts,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    private function getQuestionAnswerHistory($questionId, $userAnswersGrouped) {
        $answers = $userAnswersGrouped->get($questionId);
        if (!$answers) return [];
        return $answers->map(fn($ans) => [
            'answer_id' => $ans->answer_id,
            'attempt_number' => $ans->attempt_number ?? 1,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.customers.tryout-discussion-worksheet')
                    ->layout('layouts.blank', ['title' => 'Pembahasan: ' . $this->tryout->title]);
    }
}