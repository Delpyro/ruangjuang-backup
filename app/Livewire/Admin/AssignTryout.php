<?php

namespace App\Livewire\Admin;

use App\Models\Tryout;
use App\Models\User;
use App\Models\UserTryout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class AssignTryout extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // --- State: Tryout ---
    public $searchTryout = '';
    public $selectedTryouts = [];
    public $selectAll = false; 

    // --- State: User ---
    public $selectedUser = ''; 
    public $selectedUserName = ''; 
    public $userSearch = ''; 

    // --- State: Mutual Disable (Pengecekan Akses) ---
    public $existingTryoutIds = []; 
    public $existingUserIds = [];   

    protected $rules = [
        'selectedUser'      => 'required|exists:users,id',
        'selectedTryouts'   => 'required|array|min:1',
        'selectedTryouts.*' => 'exists:tryouts,id',
    ];

    protected $messages = [
        'selectedUser.required'     => 'Pilih satu User terlebih dahulu.',
        'selectedTryouts.required'  => 'Pilih setidaknya satu Tryout.',
        'selectedTryouts.min'       => 'Pilih setidaknya satu Tryout.',
    ];

    public function updatedSearchTryout()
    {
        $this->resetPage();
    }

    // ==========================================
    // FITUR: SEARCHABLE USER 
    // ==========================================
    
    #[Computed]
    public function dropdownUsers()
    {
        if (strlen($this->userSearch) < 2) {
            return [];
        }

        return User::where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->userSearch . '%')
                      ->orWhere('email', 'like', '%' . $this->userSearch . '%');
            })
            ->limit(5)
            ->get();
    }

    public function selectUser($id, $name)
    {
        $this->selectedUser = $id;
        $this->selectedUserName = $name;
        $this->userSearch = ''; 
        
        $this->existingTryoutIds = UserTryout::where('id_user', $id)
            ->pluck('tryout_id')
            ->toArray();
            
        $this->selectedTryouts = array_diff($this->selectedTryouts, $this->existingTryoutIds);
    }

    public function removeUser()
    {
        $this->selectedUser = '';
        $this->selectedUserName = '';
        $this->existingTryoutIds = [];
    }

    // ==========================================
    // FITUR: MUTUAL DISABLE LOGIC
    // ==========================================

    public function updatedSelectedTryouts()
    {
        if (count($this->selectedTryouts) > 0) {
            $this->existingUserIds = UserTryout::whereIn('tryout_id', $this->selectedTryouts)
                ->pluck('id_user')
                ->toArray();
        } else {
            $this->existingUserIds = [];
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $tryouts = $this->getTryoutsQuery()->get();
            $this->selectedTryouts = $tryouts->filter(function($tryout) {
                return !in_array($tryout->id, $this->existingTryoutIds);
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedTryouts = [];
        }
        
        $this->updatedSelectedTryouts();
    }

    // ==========================================
    // CORE LOGIC & ASSIGN (3 ATTEMPTS)
    // ==========================================

    private function getTryoutsQuery()
    {
        return Tryout::active()
            ->when($this->searchTryout, function ($query) {
                $query->where('title', 'like', '%' . $this->searchTryout . '%');
            })
            ->latest();
    }

    public function assign()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $assignedCount = 0;
            $user = User::find($this->selectedUser);

            foreach ($this->selectedTryouts as $tryoutId) {
                // Pastikan benar-benar belum punya (double check backend)
                if (in_array($tryoutId, $this->existingTryoutIds)) {
                    continue;
                }

                // Buat satu order ID unik untuk penanda assign manual ini
                $orderId = 'MANUAL-' . strtoupper(Str::random(8)) . '-' . time();

                // Generate 3 kali percobaan (attempt 1, 2, dan 3)
                for ($attemptNumber = 1; $attemptNumber <= 3; $attemptNumber++) {
                    UserTryout::create([
                        'id_user'      => $this->selectedUser,
                        'tryout_id'    => $tryoutId,
                        'attempt'      => $attemptNumber,
                        'order_id'     => $orderId,
                        'purchased_at' => now(),
                        'is_completed' => 0,
                    ]);
                }

                $assignedCount++;
            }

            DB::commit();

            // Reset Form
            $this->reset(['selectedUser', 'selectedUserName', 'selectedTryouts', 'selectAll', 'searchTryout', 'existingTryoutIds', 'existingUserIds']);
            
            // Trigger Auto Scroll ke atas
            $this->dispatch('scroll-to-top');

            session()->flash('success', "Berhasil memberikan $assignedCount Tryout baru kepada {$user->name}.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('scroll-to-top');
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.assign-tryout', [
            'tryouts' => $this->getTryoutsQuery()->paginate(10),
        ])->layout('layouts.admin');
    }
}