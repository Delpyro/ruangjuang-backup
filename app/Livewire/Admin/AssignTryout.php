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

    // --- State: Users (Multiple) ---
    public $selectedUsers = []; // Array of ['id' => id, 'name' => name]
    public $userSearch = ''; 

    // --- State: Mutual Disable (Pengecekan Akses) ---
    public $existingTryoutIds = []; 
    public $existingUserIds = [];   

    protected $rules = [
        'selectedUsers'     => 'required|array|min:1|max:10',
        'selectedTryouts'   => 'required|array|min:1',
        'selectedTryouts.*' => 'exists:tryouts,id',
    ];

    protected $messages = [
        'selectedUsers.required'    => 'Pilih minimal satu Pengguna.',
        'selectedUsers.min'         => 'Pilih minimal satu Pengguna.',
        'selectedUsers.max'         => 'Maksimal hanya bisa memilih 10 Pengguna sekaligus.',
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
        // Cek Maksimal 10
        if (count($this->selectedUsers) >= 10) {
            $this->dispatch('swal-toast', ['icon' => 'warning', 'title' => 'Batas Maksimal', 'text' => 'Maksimal memilih 10 pengguna sekaligus.']);
            return;
        }

        // Cek apakah sudah ada di list
        if (!collect($this->selectedUsers)->contains('id', $id)) {
            $this->selectedUsers[] = [
                'id' => $id, 
                'name' => $name
            ];
            
            $this->updateMutualLogic();
        }

        $this->userSearch = ''; 
    }

    public function removeUser($id)
    {
        // Filter out user yang dihapus
        $this->selectedUsers = collect($this->selectedUsers)
            ->reject(fn($user) => $user['id'] == $id)
            ->values()
            ->toArray();
            
        $this->updateMutualLogic();
    }

    // ==========================================
    // FITUR: MUTUAL DISABLE LOGIC
    // ==========================================

    private function updateMutualLogic()
    {
        if (empty($this->selectedUsers)) {
            $this->existingTryoutIds = [];
        } else {
            // Ambil SEMUA tryout yang dimiliki oleh siapapun di dalam selectedUsers
            $userIds = array_column($this->selectedUsers, 'id');
            $this->existingTryoutIds = UserTryout::whereIn('id_user', $userIds)
                ->pluck('tryout_id')
                ->unique()
                ->toArray();
        }

        // Bersihkan tryout yang sudah terlanjur dicentang, tapi ternyata sekarang di-disable
        $this->selectedTryouts = array_diff($this->selectedTryouts, $this->existingTryoutIds);
    }

    public function updatedSelectedTryouts()
    {
        if (count($this->selectedTryouts) > 0) {
            $this->existingUserIds = UserTryout::whereIn('tryout_id', $this->selectedTryouts)
                ->pluck('id_user')
                ->unique()
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
            $userIds = array_column($this->selectedUsers, 'id');
            $usersCount = count($userIds);

            foreach ($userIds as $userId) {
                // Ambil existing tryout secara spesifik per user untuk keamanan ganda backend
                $userExistingTryouts = UserTryout::where('id_user', $userId)->pluck('tryout_id')->toArray();

                foreach ($this->selectedTryouts as $tryoutId) {
                    if (in_array($tryoutId, $userExistingTryouts)) {
                        continue;
                    }

                    $orderId = 'MANUAL-' . strtoupper(Str::random(8)) . '-' . time();

                    for ($attemptNumber = 1; $attemptNumber <= 3; $attemptNumber++) {
                        UserTryout::create([
                            'id_user'      => $userId,
                            'tryout_id'    => $tryoutId,
                            'attempt'      => $attemptNumber,
                            'order_id'     => $orderId,
                            'purchased_at' => now(),
                            'is_completed' => 0,
                        ]);
                    }

                    $assignedCount++;
                }
            }

            DB::commit();

            // Reset Form
            $this->reset(['selectedUsers', 'selectedTryouts', 'selectAll', 'searchTryout', 'existingTryoutIds', 'existingUserIds']);
            
            // Trigger Auto Scroll ke atas
            $this->dispatch('scroll-to-top');

            session()->flash('success', "Berhasil memberikan total $assignedCount akses Tryout baru kepada $usersCount pengguna.");

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