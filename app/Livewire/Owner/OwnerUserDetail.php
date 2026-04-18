<?php

namespace App\Livewire\Owner;

use App\Models\User;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class OwnerUserDetail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $userId;
    public $user;
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Properti untuk Modal Detail Transaksi
    public $showModal = false;
    public $selectedTransaction = null;

    protected $queryString = ['search', 'statusFilter', 'perPage'];

    public function mount($id)
    {
        $this->userId = $id;
        $this->user = User::withTrashed()->findOrFail($id);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // Method untuk membuka modal dan mengambil data transaksi
    public function showDetail($transactionId)
    {
        $this->selectedTransaction = Transaction::with(['tryout', 'bundle'])
            ->find($transactionId);
            
        if ($this->selectedTransaction) {
            $this->showModal = true;
        }
    }

    // Method untuk menutup modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTransaction = null;
    }

    public function render()
    {
        $transactions = Transaction::with(['tryout', 'bundle'])
            ->where('id_user', $this->userId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_id', 'like', '%' . $this->search . '%') 
                      ->orWhere('status', 'like', '%' . $this->search . '%')
                      ->orWhereHas('tryout', function($t) {
                          $t->where('title', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('bundle', function($b) {
                          $b->where('title', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate($this->perPage);

        // Statistik ringkasan
        $totalSpent = Transaction::where('id_user', $this->userId)
            ->whereIn('status', ['success', 'berhasil', 'paid', 'settlement'])
            ->sum('amount');
        
        $totalTransactions = Transaction::where('id_user', $this->userId)->count();
        $successTransactions = Transaction::where('id_user', $this->userId)
            ->whereIn('status', ['success', 'berhasil', 'paid', 'settlement'])
            ->count();

        return view('livewire.owner.owner-user-detail', [
            'transactions' => $transactions,
            'totalSpent' => $totalSpent,
            'totalTransactions' => $totalTransactions,
            'successTransactions' => $successTransactions,
        ])->layout('layouts.owner');
    }
}