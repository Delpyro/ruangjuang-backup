<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MidtransService;

class CheckMidtransStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'midtrans:check-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek status transaksi pending yang lebih dari 1 jam ke Midtrans';

    /**
     * Execute the console command.
     */
    public function handle(MidtransService $midtransService)
    {
        $this->info('Memulai pengecekan status transaksi Midtrans...');
        
        $midtransService->checkPendingTransactions();
        
        $this->info('Pengecekan selesai.');
    }
}