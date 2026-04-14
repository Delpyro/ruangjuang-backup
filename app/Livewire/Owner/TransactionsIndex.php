<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;

#[Title('Ringkasan Transaksi Item')]
class TransactionsIndex extends Component
{
    public $tryoutSummaries = [];
    public $search = '';
    public $topSalesCount = 0; 
    public $topSellerTitles = []; 
    public $totalGlobalFree = 0;
    public $totalGlobalPaid = 0;

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

    public function loadSummaries()
    {
        $query = Transaction::success();
        $transactions = $query->with(['tryout:id,title', 'bundle:id,title'])->get();

        $summaries = [];
        $globalFree = 0;
        $globalPaid = 0;

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
                    'total_free' => 0,
                    'total_paid' => 0,
                ];
            }
            
            $summaries[$key]['total_sales'] += 1;

            $transactionTotal = $transaction->amount ?? 0; 

            if ($transactionTotal <= 0) {
                $summaries[$key]['total_free'] += 1;
                $globalFree += 1;
            } else {
                $summaries[$key]['total_paid'] += 1;
                $globalPaid += 1;
            }
        }

        $summaries = collect($summaries)
            ->sortBy([
                ['total_sales', 'desc'], 
                ['title', 'asc'],        
            ]);

        $this->tryoutSummaries = $summaries->values()->all();
        $this->totalGlobalFree = $globalFree;
        $this->totalGlobalPaid = $globalPaid;
        
        if (!empty($this->tryoutSummaries)) {
            $topSales = $this->tryoutSummaries[0]['total_sales'];
            $this->topSalesCount = $topSales;
            
            $this->topSellerTitles = [];
            
            foreach ($this->tryoutSummaries as $summary) {
                if ($summary['total_sales'] === $topSales) {
                    $this->topSellerTitles[] = $summary['title'];
                } else {
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
        // ✨ UBAH ROUTE REDIRECT KE OWNER ✨
        return $this->redirect(route('owner.transactions.detail', [
            'type' => $itemType, 
            'id' => $itemId
        ]), navigate: true); 
    }

    public function render()
    {
        // ✨ UBAH VIEW DAN LAYOUT KE OWNER ✨
        return view('livewire.owner.transactions-index')->layout('layouts.owner');
    }
}