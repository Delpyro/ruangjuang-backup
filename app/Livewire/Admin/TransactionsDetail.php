<?php

namespace App\Livewire\Admin;

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

    // --- Properti Item yang Sedang Dilihat ---
    public $itemId;
    public $itemType; // 'tryout' atau 'bundle'
    public $itemTitle;
    public $itemModel; // Tryout atau Bundle model instance

    // --- Properti untuk Modal Detail ---
    public $showModal = false;
    public ?Transaction $selectedTransaction = null; 

    // --- Properti untuk Filtering & Searching ---
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

    /**
     * Membuat Query Builder untuk Transaksi berdasarkan filter DAN ID ITEM.
     */
    protected function getBaseTransactionsQuery()
    {
        // Tentukan nama kolom kunci asing yang benar
        $columnName = $this->itemType == 'tryout' ? 'id_tryout' : 'id_bundle'; 

        return Transaction::query()
            ->with(['user:id,name', 'tryout:id,title', 'bundle:id,title'])
            
            // Filter permanen berdasarkan ID item menggunakan nama kolom yang benar
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
                    // Filter khusus transaksi Gratis
                    $query->where('amount', '<=', 0); 
                } elseif ($this->filterStatus === 'berbayar') {
                    // Filter khusus transaksi Berbayar
                    $query->where('amount', '>', 0);
                }
            })
            ->when($this->filterMonth && $this->filterMonth !== 'all', function ($query) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $this->filterMonth);
                    $query->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month);
                } catch (\Exception $e) {
                    // Abaikan jika formatnya tidak valid
                }
            })
            ->orderBy('created_at', 'desc'); 
    }

    public function render()
    {
        // Tentukan nama kolom untuk filter bulan
        $columnName = $this->itemType == 'tryout' ? 'id_tryout' : 'id_bundle';

        $transactions = $this->getBaseTransactionsQuery()->paginate(10); 

        // Buat daftar bulan untuk dropdown filter hanya dari transaksi item ini
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

        return view('livewire.admin.transactions-detail', [
            'transactions' => $transactions,
            'months' => $months,
        ])->layout('layouts.admin');
    }

    public function exportToPdf()
    {
        // 1. Ambil data dengan query yang sama (tanpa pagination)
        $transactions = $this->getBaseTransactionsQuery()->get();
        
        // 2. Tentukan nama file secara eksplisit
        $fileName = 'transactions_' . $this->itemType . '_' . $this->itemModel->id;
        
        if ($this->filterStatus !== 'all') {
            $fileName .= '_' . $this->filterStatus;
        }
        if ($this->filterMonth !== 'all') {
            $fileName .= '_' . $this->filterMonth;
        }
        $fileName .= '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // 3. Muat view yang didedikasikan untuk PDF dengan data transaksi
        $pdf = PDF::loadView('pdf.transactions-report', [
            'transactions' => $transactions,
            'filterStatus' => $this->filterStatus,
            'filterMonth' => $this->filterMonth,
            'itemTitle' => $this->itemTitle, 
        ]);

        // 4. Download PDF
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