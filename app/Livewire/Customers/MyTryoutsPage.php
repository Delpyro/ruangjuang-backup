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
    // ✨ BARU: Properti kategori
    public $category = 'all'; 
    public $sort = 'latest'; 
    public $selectedTryoutId = null;
    public $selectedUserTryoutId = null; 

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'filter' => ['except' => 'all', 'as' => 'f'],
        'category' => ['except' => 'all', 'as' => 'c'], // ✨ BARU: Binding query string
        'sort' => ['except' => 'latest', 'as' => 's'],
    ];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilter() { $this->resetPage(); }
    public function updatedCategory() { $this->resetPage(); } // ✨ BARU
    public function updatedSort() { $this->resetPage(); }
    public function setFilter($value) { $this->filter = $value; $this->resetPage(); }
    public function setSort($value) { $this->sort = $value; $this->resetPage(); }
    
    // ✨ BARU: Reset Filters mencakup $category
    public function resetFilters() { $this->search = ''; $this->filter = 'all'; $this->category = 'all'; $this->sort = 'latest'; $this->resetPage(); }


    /**
     * Menerima ID Tryout Model, mencari Attempt terendah yang tersedia (belum dimulai), 
     * atau mengarahkan ke yang sedang berjalan.
     */
    public function confirmStart($tryoutId)
    {
        $user = Auth::user();

        // Cari ATTEMPT TERENDAH yang BELUM SELESAI
        $nextAttemptPivot = UserTryout::where('id_user', $user->id)
                                     ->where('tryout_id', $tryoutId)
                                     ->where('is_completed', false)
                                     ->orderBy('attempt', 'asc')
                                     ->first();

        // 1. Cek apakah ada attempt yang tersedia/sedang berjalan
        if ($nextAttemptPivot) {
            
            $tryout = Tryout::find($tryoutId);

            if ($nextAttemptPivot->started_at) {
                // Jika sudah dimulai tapi belum selesai, lanjutkan
                // ✅ Menggunakan route 'tryout.start' dengan attempt number
                return $this->redirect(route('tryout.start', [ 
                    'tryout' => $tryout->slug,
                    'attempt' => $nextAttemptPivot->attempt 
                ]));
            }
            
            // Jika belum dimulai, tampilkan modal konfirmasi
            $this->selectedUserTryoutId = $nextAttemptPivot->id; 
            $this->selectedTryoutId = $tryoutId; 
            $this->dispatch('show-copyright-modal');
            return;
        }
        
        // 2. Jika tidak ada attempt yang belum selesai (semua sudah complete)
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

    /**
     * Method untuk memulai tryout setelah user setuju di modal.
     * ✅ PERBAIKAN: Menggunakan $this->redirect() untuk menghindari bug loading Livewire.
     */
    public function startTryout()
    {
        if (!$this->selectedUserTryoutId) {
            return; 
        }

        $user = Auth::user();
        
        // Ambil data pivot yang spesifik
        $userTryout = UserTryout::with('tryout:id,slug,duration')
                             ->where('id_user', $user->id)
                             ->where('id', $this->selectedUserTryoutId)
                             ->first();
        
        $tryout = $userTryout?->tryout;
        
        if (!$userTryout || !$tryout || $tryout->duration === null || $userTryout->started_at) {
            $this->dispatch('show-toast', ['message' => 'Gagal memulai. Attempt sudah dimulai atau data tidak valid.', 'type' => 'error']);
            return;
        }
        
        // 1. Update database: Tanda waktu mulai dan waktu selesai
        $now = Carbon::now();
        $endTime = $now->copy()->addMinutes($tryout->duration);

        $userTryout->update([
            'started_at' => $now,
            'ended_at' => $endTime,
            'is_completed' => false, 
        ]);
        
        // 2. Siapkan data timer dan simpan di flash session
        session()->flash('tryout_timer_data', [
            'storageKey' => 'tryout_timer_' . $userTryout->id, 
            'data' => [
                'started_at' => $now->toIso8601String(),
                'ended_at' => $endTime->toIso8601String(),
                'duration' => $tryout->duration,
                'user_tryout_id' => $userTryout->id,
            ],
        ]);
        
        // 3. Lakukan REDIRECT Livewire
        $redirectUrl = route('tryout.start', [
            'tryout' => $tryout->slug, 
            'attempt' => $userTryout->attempt 
        ]);
        
        // Menggunakan $this->redirect() akan menghentikan render Livewire dan memaksa navigasi
        return $this->redirect($redirectUrl); 
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Sub-query untuk menemukan ATTEMPT YANG HARUS DITAMPILKAN per Tryout ID
        $nextAttemptSubQuery = UserTryout::select('tryout_id', 
                                                 DB::raw('MIN(CASE WHEN is_completed = 0 THEN attempt ELSE NULL END) as next_available_attempt'),
                                                 DB::raw('MAX(CASE WHEN is_completed = 1 THEN id ELSE NULL END) as last_completed_id')
                                             )
                                             ->where('id_user', $user->id)
                                             ->groupBy('tryout_id');
        
        // 2. Ambil Tryout Models unik, filter, dan join sub-query
        $tryouts = Tryout::query()
            ->joinSub($nextAttemptSubQuery, 'attempts_status', function ($join) {
                 $join->on('tryouts.id', '=', 'attempts_status.tryout_id');
            })
            ->withCount(['activeQuestions as active_questions_count'])
            ->select('tryouts.*', 'attempts_status.next_available_attempt', 'attempts_status.last_completed_id')
            
            // Terapkan filter dan search
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter === 'hots', fn ($q) => $q->where('is_hots', true))
            ->when($this->filter === 'regular', fn ($q) => $q->where('is_hots', false))
            
            // ✨ BARU: Query filtering Kategori
            ->when($this->category !== 'all', fn ($q) => $q->where('category', $this->category))
            
            // Sortir
            ->when($this->sort === 'latest', fn ($q) => $q->orderBy('tryouts.id', 'desc'))
            ->when($this->sort === 'purchased_date', fn ($q) => $q->orderBy('tryouts.id', 'asc'))
            
            ->paginate(12);

        // 3. Eager load status pivot yang benar untuk setiap Tryout Model
        $tryouts->getCollection()->transform(function ($tryout) use ($user) {
            
            // Ambil ID pivot yang paling relevan (yang belum selesai/sudah selesai terakhir)
            if ($tryout->next_available_attempt) {
                // Ada attempt yang belum selesai. Cari ID pivot-nya.
                $pivot = UserTryout::where('id_user', $user->id)
                                    ->where('tryout_id', $tryout->id)
                                    ->where('attempt', $tryout->next_available_attempt)
                                    ->first();
                $tryout->user_progress = $pivot;

            } elseif ($tryout->last_completed_id) {
                // Semua attempt sudah selesai. Ambil hasil terakhir.
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