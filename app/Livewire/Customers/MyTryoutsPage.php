<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use App\Models\Tryout;
use App\Models\UserTryout; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; 

class MyTryoutsPage extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all'; 
    public $category = 'all'; 
    public $sort = 'latest'; 
    public $selectedTryoutId = null;
    public $selectedUserTryoutId = null; 

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'filter' => ['except' => 'all', 'as' => 'f'],
        'category' => ['except' => 'all', 'as' => 'c'], 
        'sort' => ['except' => 'latest', 'as' => 's'],
    ];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilter() { $this->resetPage(); }
    public function updatedCategory() { $this->resetPage(); } 
    public function updatedSort() { $this->resetPage(); }
    public function setFilter($value) { $this->filter = $value; $this->resetPage(); }
    public function setSort($value) { $this->sort = $value; $this->resetPage(); }
    
    public function resetFilters() { $this->search = ''; $this->filter = 'all'; $this->category = 'all'; $this->sort = 'latest'; $this->resetPage(); }

    public function confirmStart($tryoutId)
    {
        // ... (Kode confirmStart tetap sama tidak ada perubahan) ...
        $user = Auth::user();

        $nextAttemptPivot = UserTryout::where('id_user', $user->id)
                                     ->where('tryout_id', $tryoutId)
                                     ->where('is_completed', false)
                                     ->orderBy('attempt', 'asc')
                                     ->first();

        if ($nextAttemptPivot) {
            $tryout = Tryout::find($tryoutId);

            if ($nextAttemptPivot->started_at) {
                return $this->redirect(route('tryout.start', [ 
                    'tryout' => $tryout->slug,
                    'attempt' => $nextAttemptPivot->attempt 
                ]));
            }
            
            $this->selectedUserTryoutId = $nextAttemptPivot->id; 
            $this->selectedTryoutId = $tryoutId; 
            $this->dispatch('show-copyright-modal');
            return;
        }
        
        $totalAttempts = UserTryout::where('id_user', $user->id)
                                   ->where('tryout_id', $tryoutId)
                                   ->count();
        
        $completedAttempts = UserTryout::where('id_user', $user->id)
                                       ->where('tryout_id', $tryoutId)
                                       ->where('is_completed', true)
                                       ->count();

        if ($totalAttempts === $completedAttempts) {
             $this->dispatch('show-toast', ['message' => 'Anda sudah menyelesaikan semua kesempatan tryout ini.', 'type' => 'info']);
        } else {
             $this->dispatch('show-toast', ['message' => 'Tidak ada kesempatan tryout yang tersedia/belum dimulai.', 'type' => 'error']);
        }
    }

    public function startTryout()
    {
        // ... (Kode startTryout tetap sama tidak ada perubahan) ...
        if (!$this->selectedUserTryoutId) {
            return; 
        }

        $user = Auth::user();
        
        $userTryout = UserTryout::with('tryout:id,slug,duration')
                             ->where('id_user', $user->id)
                             ->where('id', $this->selectedUserTryoutId)
                             ->first();
        
        $tryout = $userTryout?->tryout;
        
        if (!$userTryout || !$tryout || $tryout->duration === null || $userTryout->started_at) {
            $this->dispatch('show-toast', ['message' => 'Gagal memulai. Attempt sudah dimulai atau data tidak valid.', 'type' => 'error']);
            return;
        }
        
        $now = Carbon::now();
        $endTime = $now->copy()->addMinutes($tryout->duration);

        $userTryout->update([
            'started_at' => $now,
            'ended_at' => $endTime,
            'is_completed' => false, 
        ]);
        
        session()->flash('tryout_timer_data', [
            'storageKey' => 'tryout_timer_' . $userTryout->id, 
            'data' => [
                'started_at' => $now->toIso8601String(),
                'ended_at' => $endTime->toIso8601String(),
                'duration' => $tryout->duration,
                'user_tryout_id' => $userTryout->id,
            ],
        ]);
        
        $redirectUrl = route('tryout.start', [
            'tryout' => $tryout->slug, 
            'attempt' => $userTryout->attempt 
        ]);
        
        return $this->redirect($redirectUrl); 
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Sub-query untuk menemukan ATTEMPT YANG HARUS DITAMPILKAN per Tryout ID
        $nextAttemptSubQuery = UserTryout::select('tryout_id', 
            DB::raw('MIN(CASE WHEN is_completed = 0 THEN attempt ELSE NULL END) as next_available_attempt'),
            DB::raw('MAX(CASE WHEN is_completed = 1 THEN id ELSE NULL END) as last_completed_id'),
            // ✨ BARU: Cek apakah attempt tersebut sedang berjalan (started_at tidak null)
            DB::raw('MAX(CASE WHEN is_completed = 0 AND started_at IS NOT NULL THEN 1 ELSE 0 END) as is_in_progress')
        )
        ->where('id_user', $user->id)
        ->groupBy('tryout_id');
        
        // 2. Ambil Tryout Models unik, filter, dan join sub-query
        $tryouts = Tryout::query()
            ->joinSub($nextAttemptSubQuery, 'attempts_status', function ($join) {
                 $join->on('tryouts.id', '=', 'attempts_status.tryout_id');
            })
            ->withCount(['activeQuestions as active_questions_count'])
            // ✨ BARU: Masukkan field is_in_progress ke dalam select
            ->select('tryouts.*', 'attempts_status.next_available_attempt', 'attempts_status.last_completed_id', 'attempts_status.is_in_progress')
            
            // Terapkan filter dan search
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter === 'hots', fn ($q) => $q->where('is_hots', true))
            ->when($this->filter === 'regular', fn ($q) => $q->where('is_hots', false))
            ->when($this->category !== 'all', fn ($q) => $q->where('category', $this->category))
            
            // ✨ BARU: LOGIKA PENGURUTAN BERDASARKAN STATUS PENGERJAAN
            ->orderByRaw('
                CASE
                    WHEN attempts_status.next_available_attempt IS NULL THEN 9999 -- Jika semua selesai, lempar paling bawah
                    WHEN attempts_status.is_in_progress = 0 THEN attempts_status.next_available_attempt * 10 -- Belum dikerjakan (Prioritas Tertinggi per Attempt)
                    ELSE (attempts_status.next_available_attempt * 10) + 1 -- Sedang dikerjakan (Di bawah yang belum dikerjakan pada Attempt yang sama)
                END ASC
            ')

            // ✨ Sortir Kedua: Berdasarkan filter Terbaru/Terlama (ID)
            ->when($this->sort === 'latest', fn ($q) => $q->orderBy('tryouts.id', 'desc'))
            ->when($this->sort === 'purchased_date', fn ($q) => $q->orderBy('tryouts.id', 'asc'))
            
            ->paginate(12);

        // 3. Eager load status pivot yang benar untuk setiap Tryout Model
        $tryouts->getCollection()->transform(function ($tryout) use ($user) {
            if ($tryout->next_available_attempt) {
                $pivot = UserTryout::where('id_user', $user->id)
                                    ->where('tryout_id', $tryout->id)
                                    ->where('attempt', $tryout->next_available_attempt)
                                    ->first();
                $tryout->user_progress = $pivot;

            } elseif ($tryout->last_completed_id) {
                $tryout->user_progress = UserTryout::find($tryout->last_completed_id);
            } else {
                $tryout->user_progress = null;
            }
            
            return $tryout;
        });

        return view('livewire.customers.my-tryouts-page', compact('tryouts'))
            ->layout('layouts.app');
    }
}