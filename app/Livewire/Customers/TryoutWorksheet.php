<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Ranking;
use App\Models\TryoutCategoryScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class TryoutWorksheet extends Component
{
    public Tryout $tryout;
    public $userTryout;
    public $endTime;
    public $title;

    // State untuk Sinkronisasi dengan Alpine.js
    // allQuestions disimpan sebagai array agar hydration Livewire cepat (tidak berat di proses serialisasi)
    public array $allQuestions = []; 
    
    // userAnswers adalah "Source of Truth" (Data resmi yang ada di Database)
    // Digunakan frontend untuk reset pilihan jika user pindah soal tanpa klik simpan
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

        // 2. Proteksi Akses & Status Selesai
        if (!$userTryout || $userTryout->is_completed) {
            return $this->redirect(route('tryout.my-tryouts'), navigate: true);
        }

        // 3. Proteksi Waktu (Cek Server-Side)
        if (Carbon::now()->isAfter($userTryout->ended_at)) {
            $this->forceFinishExam($userTryout);
            return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
        }

        $this->userTryout = $userTryout;
        $this->endTime = $userTryout->ended_at->toIso8601String();

        // 4. Load Data (Eager Loading & Stripping)
        $this->loadInitialData();
        $this->calculateProgress();
    }

    /**
     * Memuat soal dan progres jawaban resmi dari Database.
     */
    private function loadInitialData()
    {
        // Ambil soal & jawaban (Menyesuaikan kolom DB Bank NTT: id_question_categories, id_question_sub_category)
        $questions = $this->tryout->activeQuestions()
            ->with(['subCategory', 'answers' => function($query) {
                $query->select('id', 'id_question', 'answer'); 
            }])
            ->get(['id', 'id_question_categories', 'id_question_sub_category', 'question', 'image']);

        $this->allQuestions = $questions->toArray();
        $this->totalQuestions = count($this->allQuestions);

        // Ambil data jawaban yang SUDAH TERSIMPAN di Database
        $this->userAnswers = UserAnswer::where('user_tryout_id', $this->userTryout->id)
            ->get(['question_id', 'answer_id', 'is_doubtful'])
            ->keyBy('question_id')
            ->map(fn($item) => [
                'answer_id' => $item->answer_id,
                'is_doubtful' => (bool)$item->is_doubtful,
            ])->toArray();
    }

    /**
     * METHOD SIMPAN: Hanya dipanggil saat user klik "Simpan & Lanjut" di View.
     */
    public function saveAnswer($questionId, $answerId, $isDoubtful = false)
    {
        try {
            if (!$this->userTryout || !$questionId) return;

            // Security: Hitung poin di server berdasarkan answer_id
            $points = 0;
            if ($answerId) {
                $points = DB::table('answers')->where('id', $answerId)->value('points') ?? 0;
            }

            // Database Persistence (Urutan sesuai Composite Index: user_tryout_id + question_id)
            UserAnswer::updateOrCreate(
                [
                    'user_tryout_id' => $this->userTryout->id, 
                    'question_id'    => $questionId
                ],
                [
                    'id_user'     => Auth::id(),
                    'answer_id'   => $answerId,
                    'is_doubtful' => $isDoubtful,
                    'score'       => $points
                ]
            );

            // Sync ke Properti Livewire agar Sidebar berubah warna & Progress terupdate
            $this->userAnswers[$questionId] = [
                'answer_id' => $answerId,
                'is_doubtful' => $isDoubtful,
            ];

            $this->calculateProgress();

        } catch (\Exception $e) {
            logger()->error("Gagal simpan jawaban User: " . Auth::id() . " | Error: " . $e->getMessage());
        }
    }

    /**
     * Update persentase progres berdasarkan data resmi di DB.
     */
    private function calculateProgress()
    {
        $answeredCount = collect($this->userAnswers)->whereNotNull('answer_id')->count();
        $this->progressPercent = $this->totalQuestions > 0 
            ? round(($answeredCount / $this->totalQuestions) * 100, 1) 
            : 0;
    }

    /**
     * Menutup sesi ujian.
     */
    public function finishExam()
    {
        $this->forceFinishExam($this->userTryout);
        session()->flash('success', 'Ujian telah dikumpulkan.');
        return $this->redirect(route('tryout.my-results', $this->tryout->slug), navigate: true);
    }

    /**
     * Logika Kalkulasi Skor (Heavy Task - Dijalankan di akhir saja).
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
                    $catId = $q->category->id ?? 0;
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

                // 1. Simpan Ranking
                Ranking::updateOrCreate(
                    ['id_user' => Auth::id(), 'tryout_id' => $userTryout->tryout_id],
                    ['score' => $totalScore]
                );

                // 2. Simpan Rapor Per Kategori
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