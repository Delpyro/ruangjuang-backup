<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;

#[Title('Ringkasan Transaksi Item')]
class TransactionsIndex extends Component
{
    // Properti untuk menampung data ringkasan
    public $tryoutSummaries = [];
    
    // Properti untuk Pencarian
    public $search = '';

    // Properti untuk menyimpan jumlah penjualan tertinggi
    public $topSalesCount = 0; 

    // Properti untuk menyimpan daftar judul item yang seri di posisi tertinggi
    public $topSellerTitles = []; 

    // Query String untuk URL
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadSummaries();
    }
    
    public function updatedSearch()
    {
        $this->loadSummaries(); 
    }

    /**
     * Mengambil ringkasan total transaksi yang Success per Tryout/Bundle.
     */
    public function loadSummaries()
    {
        $query = Transaction::success();
        $transactions = $query->with(['tryout:id,title,price', 'bundle:id,title,price'])->get();

        $summaries = [];

        foreach ($transactions as $transaction) {
            
            $item = null;
            $itemType = null;

            if ($transaction->id_bundle && $transaction->bundle) {
                $item = $transaction->bundle;
                $itemType = 'bundle';
            } elseif ($transaction->id_tryout && $transaction->tryout) {
                $item = $transaction->tryout;
                $itemType = 'tryout';
            }
            
            if (!$item) {
                continue;
            }

            $itemId = $item->id;
            $itemTitle = $item->title ?? 'Item Dihapus';
            
            // Filter berdasarkan nama Tryout/Bundle
            if ($this->search && !Str::contains(strtolower($itemTitle), strtolower($this->search))) {
                continue;
            }

            $key = "{$itemType}-{$itemId}";

            if (!isset($summaries[$key])) {
                $summaries[$key] = [
                    'id' => $itemId,
                    'type' => $itemType,
                    'title' => $itemTitle,
                    'total_sales' => 0,
                ];
            }
            
            $summaries[$key]['total_sales'] += 1;
        }

        // Urutkan: 1. Total Sales (DESC), 2. Title (ASC) sebagai tie-breaker
        $summaries = collect($summaries)
            ->sortBy([
                ['total_sales', 'desc'], 
                ['title', 'asc'],        
            ]);

        $this->tryoutSummaries = $summaries->values()->all();
        
        // SET NILAI PENJUALAN TERTINGGI & DAFTAR JUDUL TERLARIS
        if (!empty($this->tryoutSummaries)) {
            $topSales = $this->tryoutSummaries[0]['total_sales'];
            $this->topSalesCount = $topSales;
            
            $this->topSellerTitles = [];
            
            foreach ($this->tryoutSummaries as $summary) {
                // Kumpulkan semua item yang memiliki penjualan sama dengan yang tertinggi
                if ($summary['total_sales'] === $topSales) {
                    $this->topSellerTitles[] = $summary['title'];
                } else {
                    // Berhenti setelah total sales menurun (karena sudah diurutkan)
                    break; 
                }
            }
        } else {
            $this->topSalesCount = 0;
            $this->topSellerTitles = [];
        }
    }
    
    public function goToDetail($itemType, $itemId)
    {
        // Fungsi ini dipanggil oleh tombol
        return $this->redirect(route('admin.transactions.detail', [
            'type' => $itemType, 
            'id' => $itemId
        ]), navigate: true); 
    }

    public function render()
    {
        return view('livewire.admin.transactions-index')->layout('layouts.admin');
    }
}