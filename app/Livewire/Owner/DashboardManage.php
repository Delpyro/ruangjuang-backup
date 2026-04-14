<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use App\Models\User;

class DashboardManage extends Component
{
    public $userCount;
    public $recentUsers;

    public function mount()
    {
        $this->userCount = User::count();
        $this->recentUsers = User::latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.owner.dashboard-manage')
            ->layout('layouts.owner'); // ✨ Pastikan menggunakan layout owner
    }
}