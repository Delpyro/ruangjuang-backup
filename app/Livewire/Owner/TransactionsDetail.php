<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use App\Services\MidtransService; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Tryout;
use App\Models\Bundle;
use Livewire\Attributes\Title;

#[Title('Detail Transaksi Item')]
class TransactionsDetail extends Component 
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $itemId;
    public $itemType; 
    public $itemTitle;
    public $itemModel; 

    public $showModal = false;
    public ?Transaction $selectedTransaction = null; 

    public $search = '';
    public $filterStatus = 'all'; 
    public $filterMonth = 'all'; 

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all', 'as' => 'status'],
        'filterMonth' => ['except' => 'all', 'as' => 'month'],
    ];

    public function mount($id, $type)
    {
        $this->itemId = $id;
        $this->itemType = strtolower($type);

        if ($this->itemType == 'tryout') {
            $this->itemModel = Tryout::findOrFail($id);
        } elseif ($this->itemType == 'bundle') {
            $this->itemModel = Bundle::findOrFail($id);
        } else {
            abort(404, 'Tipe item tidak valid.');
        }

        $this->itemTitle = $this->itemModel->title;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }
    
    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    protected function getBaseTransactionsQuery()
    {
        $columnName = $this->itemType == 'tryout' ? 'id_tryout' : 'id_bundle'; 

        return Transaction::query()
            ->with(['user:id,name', 'tryout:id,title', 'bundle:id,title'])
            ->where($columnName, $this->itemId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_id', 'like', '%' . $this->search . '%')
                        ->orWhere('payment_method', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($query) {
                if ($this->filterStatus === 'success') {
                    $query->success();
                } elseif ($this->filterStatus === 'pending') {
                    $query->pending();
                } elseif ($this->filterStatus === 'failed') {
                    $query->failed();
                } elseif ($this->filterStatus === 'gratis') {
                    $query->where('amount', '<=', 0); 
                } elseif ($this->filterStatus === 'berbayar') {
                    $query->where('amount', '>', 0);
                }
            })
            ->when($this->filterMonth && $this->filterMonth !== 'all', function ($query) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $this->filterMonth);
                    $query->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month);
                } catch (\Exception $e) {}
            })
            ->orderBy('created_at', 'desc'); 
    }

    public function render()
    {
        $columnName = $this->itemType == 'tryout' ? 'id_tryout' : 'id_bundle';

        $transactions = $this->getBaseTransactionsQuery()->paginate(10); 

        $months = Transaction::where($columnName, $this->itemId) 
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'))
            ->whereNotNull('created_at')
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month_year);
                return [
                    'value' => $item->month_year,
                    'label' => $date->isoFormat('MMMM YYYY')
                ];
            });

        // ✨ UBAH VIEW DAN LAYOUT KE OWNER ✨
        return view('livewire.owner.transactions-detail', [
            'transactions' => $transactions,
            'months' => $months,
        ])->layout('layouts.owner');
    }

    public function exportToPdf()
    {
        $transactions = $this->getBaseTransactionsQuery()->get();
        $fileName = 'transactions_' . $this->itemType . '_' . $this->itemModel->id;
        
        if ($this->filterStatus !== 'all') {
            $fileName .= '_' . $this->filterStatus;
        }
        if ($this->filterMonth !== 'all') {
            $fileName .= '_' . $this->filterMonth;
        }
        $fileName .= '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        $pdf = PDF::loadView('pdf.transactions-report', [
            'transactions' => $transactions,
            'filterStatus' => $this->filterStatus,
            'filterMonth' => $this->filterMonth,
            'itemTitle' => $this->itemTitle, 
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName); 
    }
    
    public function openModal($id)
    {
        $this->selectedTransaction = Transaction::with(['user', 'tryout', 'bundle'])->find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTransaction = null; 
    }

    public function syncStatus($orderId, MidtransService $midtransService)
    {
        if (!$orderId) {
            session()->flash('error', 'Order ID tidak ditemukan.');
            return;
        }

        try {
            $statusResult = $midtransService->getStatus($orderId);

            if ($statusResult['success']) {
                $midtransService->handleNotification((array)$statusResult['response']);
                session()->flash('success', 'Status transaksi ' . $orderId . ' berhasil disinkronkan.');
            } else {
                $transaction = Transaction::where('order_id', $orderId)->first();
                if ($transaction && $transaction->isPending()) {
                    $transaction->update(['status' => 'expire']);
                    session()->flash('success', 'Transaksi ' . $orderId . ' ditandai sebagai expired (404).');
                } else {
                    session()->flash('error', 'Gagal sinkronisasi: ' . $statusResult['error']);
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }
}