<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use App\Models\Tryout;
use App\Models\Bundle;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OwnerPayment extends Component
{
    // Properti untuk menerima slug tryout ATAU bundle
    public $tryout_slug = null;
    public $bundle_slug = null;
    
    // Properti untuk menyimpan instance item yang dibeli
    public $item; 
    public $transaction;
    
    // Properti untuk Midtrans
    public $snapToken;
    public $clientKey; // Untuk dikirim ke frontend
    public $orderId;
    
    // UI/State Properties
    public $isLoading = true;
    public $error = null;
    public $debugInfo = [];
    
    protected $midtransService;

    // Listener untuk event dari Blade setelah Midtrans Snap sukses
    protected $listeners = ['paymentSuccess' => 'handlePaymentSuccess'];

    public function boot(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
        $this->clientKey = config('services.midtrans.client_key');
    }

    public function mount($tryout_slug = null, $bundle_slug = null)
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'));
        }
        
        $user = Auth::user();

        try {
            // --- 1. Tentukan Item yang Dibeli (Tryout atau Bundle) ---
            if ($tryout_slug) {
                $this->item = Tryout::active()
                    ->where('slug', $tryout_slug)
                    ->withCount(['activeQuestions as active_questions_count'])
                    ->firstOrFail();
            } elseif ($bundle_slug) {
                $this->item = Bundle::available()
                    ->where('slug', $bundle_slug)
                    ->with('tryouts')
                    ->firstOrFail();
            } else {
                 throw new \Exception('Item slug is missing.');
            }

            // Tentukan kolom dan rute berdasarkan tipe item
            $typeColumn = $this->item instanceof Tryout ? 'id_tryout' : 'id_bundle';
            $redirectRouteName = $this->item instanceof Tryout ? 'tryout.detail' : 'bundle.detail';

            // --- 2. Cek Kepemilikan (Sudah Beli) ---
            $purchaserCheck = Transaction::where('id_user', auth()->id())
                ->where($typeColumn, $this->item->id)
                ->where('status', Transaction::STATUS_SETTLEMENT)
                ->first();

            if ($purchaserCheck) {
                return redirect()->route($redirectRouteName, $this->item->slug)
                    ->with('success', 'Anda sudah membeli ' . $this->item->title);
            }
            
            // --- 3. Cek atau Buat Transaksi Pending ---
            $this->transaction = Transaction::where('id_user', auth()->id())
                ->where($typeColumn, $this->item->id)
                ->where('status', Transaction::STATUS_PENDING)
                ->first();

            if (!$this->transaction) {
                $data = [
                    'id_user' => auth()->id(),
                    'amount' => $this->item->final_price,
                    'payment_method' => 'midtrans',
                    'status' => Transaction::STATUS_PENDING,
                    'ip_user' => request()->ip(),
                ];
                $data[$typeColumn] = $this->item->id;
                $this->transaction = Transaction::create($data);
            }
            
            // --- 4. Handle Harga Gratis ---
            if ($this->item->final_price <= 0) {
                return $this->processFreePurchase();
            }

            // --- 5. Generate Payment Data ---
            $this->initializePayment();

        } catch (\Exception $e) {
            Log::error('💥 PaymentPage Mount Error', ['error' => $e->getMessage()]);
            $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->isLoading = false;
        }
    }
    
    // Dipanggil saat Midtrans Pop-up SUKSES atau untuk pembelian GRATIS
    public function handlePaymentSuccess()
    {
        // Logika pengalihan setelah pembayaran sukses (atau gratis).
        // Granting access dilakukan oleh Midtrans Notification Controller/Service.
        
        session()->flash('success', 'Pembelian ' . $this->item->title . ' berhasil! Item sudah ditambahkan ke akun Anda.');
        
        if ($this->item instanceof Tryout) {
             return $this->redirect(route('tryout.my-tryouts'), navigate: true);
        } else { // instanceof Bundle
             return $this->redirect(route('dashboard'), navigate: true);
        }
    }

    protected function processFreePurchase()
    {
        // Update transaksi pending menjadi settlement (seperti sukses)
        if ($this->transaction) {
            
            // Tentukan prefix untuk Order ID
            $typePrefix = $this->item instanceof Tryout ? 'TRYOUT' : 'BUNDLE';
            
            // Generate Order ID unik untuk item gratis, agar tidak NULL
            // Kita gunakan 'FREE' sebagai penanda
            $freeOrderId = 'FREE-' . $typePrefix . '-' . $this->transaction->id . '-' . time();

            $this->transaction->update([
                'status' => Transaction::STATUS_SETTLEMENT,
                'order_id' => $freeOrderId, // <-- INI SOLUSINYA
                'settlement_time' => now(), // Waktu pembelian
                'payment_type' => 'free', // Menandakan ini gratis
            ]);

            // Refresh model untuk memastikan $this->transaction
            // membawa data yang sudah di-update (terutama order_id)
            $this->transaction->refresh();
        }
        
        // Panggil grantAccess SETELAH transaksi di-update dengan order_id
        // Sekarang $this->transaction->order_id sudah tidak null
        $this->midtransService->grantAccess($this->transaction);
        
        return $this->handlePaymentSuccess();
    }

    public function initializePayment()
    {
        try {
            $midtransService = new MidtransService();
            
            $paymentData = $midtransService->createTransaction(
                $this->transaction, 
                $this->item, // Mengirim instance Tryout atau Bundle
                auth()->user()
            );

            if ($paymentData['success']) {
                $this->snapToken = $paymentData['snap_token'];
                $this->orderId = $paymentData['order_id'];
                
                // Panggil JS untuk membuka Midtrans Snap
                $this->dispatch('showMidtransSnap', 
                    snapToken: $this->snapToken, 
                    clientKey: $this->clientKey
                );
            } else {
                throw new \Exception($paymentData['error'] ?? 'Unknown error from Midtrans');
            }

            $this->isLoading = false;

        } catch (\Exception $e) {
            Log::error('💥 Initialize Payment Failed', ['error' => $e->getMessage()]);
            $this->error = 'Gagal memproses pembayaran: ' . $e->getMessage();
            $this->isLoading = false;
        }
    }

    public function render()
    {
        // Debug info hanya untuk local environment
        if (app()->environment('local') && $this->item) {
            $this->debugInfo = [
                'item' => [
                    'id' => $this->item->id,
                    'title' => $this->item->title,
                    'price' => $this->item->final_price,
                    'type' => $this->item instanceof Bundle ? 'Bundle' : 'Tryout',
                ],
                'transaction' => $this->transaction ? [
                    'id' => $this->transaction->id,
                    'order_id_db' => $this->transaction->order_id,
                    'amount' => $this->transaction->amount,
                ] : null,
                'snap_token_init' => $this->snapToken ? substr($this->snapToken, 0, 30) . '...' : 'Pending Init',
                'client_key' => $this->clientKey,
                'current_order_id' => $this->orderId
            ];
        }

        return view('livewire.owner.owner-payment', [
            'item' => $this->item,
            'isBundle' => $this->item instanceof Bundle
        ])->layout('layouts.app');
    }
}
