<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;
use App\Models\Transaction;
use App\Models\Tryout;
use App\Models\Bundle; // Tambahkan import Bundle
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; 
use Exception;

class MidtransService
{
    // Konstan untuk Fraud Status (opsional, tapi baik untuk konsistensi)
    const FRAUD_ACCEPT = 'accept';
    const FRAUD_CHALLENGE = 'challenge';
    const FRAUD_DENY = 'deny';
    
    public function __construct()
    {
        $this->initializeMidtrans();
    }

    /**
     * Initialize Midtrans configuration
     */
    private function initializeMidtrans()
    {
        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$clientKey = config('services.midtrans.client_key');
            Config::$isProduction = config('services.midtrans.is_production', false);
            Config::$isSanitized = config('services.midtrans.is_sanitized', true);
            Config::$is3ds = config('services.midtrans.is_3ds', true);
            
            Log::info('🎯 Midtrans Config Loaded', [
                'env' => Config::$isProduction ? 'PRODUCTION' : 'SANDBOX',
                'server_key' => substr(Config::$serverKey, 0, 15) . '...',
                'client_key' => Config::$clientKey,
                'merchant_id' => 'G681396961'
            ]);
        } catch (Exception $e) {
            Log::error('❌ Midtrans Config Failed: ' . $e->getMessage());
        }
    }

    /**
     * Create new Snap transaction for Tryout or Bundle.
     * * @param Transaction $transaction The local transaction record.
     * @param Tryout|Bundle $item The purchased item (Tryout or Bundle instance).
     * @param User $user The purchasing user.
     * @return array
     */
    public function createTransaction(Transaction $transaction, $item, User $user): array
    {
        DB::beginTransaction();
        
        try {
            // Tentukan tipe item untuk ID Pesanan dan detail
            $isTryout = $item instanceof Tryout;
            $typePrefix = $isTryout ? 'TRYOUT' : 'BUNDLE';
            $itemSlugPrefix = $isTryout ? 'TRYOUT-' : 'BUNDLE-';

            // Generate unique order ID
            $orderId = $typePrefix . '-' . $transaction->id . '-' . time();
            
            Log::info('🔄 Creating Midtrans Transaction', [
                'order_id' => $orderId,
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'item_id' => $item->id,
                'item_type' => $typePrefix,
                'amount' => $transaction->amount
            ]);

            // Transaction details
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $transaction->amount,
            ];

            // Customer details
            $customerDetails = [
                'first_name' => $this->sanitizeName($user->name),
                'email' => $user->email,
                'phone' => $this->sanitizePhone($user->phone ?? '081234567890'),
            ];

            // Item details (Universal)
            $itemDetails = [
                [
                    'id' => $itemSlugPrefix . $item->id,
                    'price' => $transaction->amount,
                    'quantity' => 1,
                    'name' => $this->sanitizeProductName($item->title),
                    'category' => 'Education',
                    'brand' => 'Bercnada'
                ]
            ];

            // Snap parameters
            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'hours',
                    'duration' => 24
                ]
            ];

            // Log::info('📦 Midtrans Request Params', $params);

            // Generate Snap token
            $snapToken = Snap::getSnapToken($params);
            
            if (empty($snapToken)) {
                throw new Exception('Midtrans returned empty Snap token');
            }

            // Update transaction record
            $transaction->update([
                'order_id' => $orderId,
                'expired_at' => now()->addHours(24),
                'metadata' => [
                    'snap_token_generated_at' => now()->toISOString(),
                    'item_title' => $item->title, // Universal item title
                    'item_type' => $typePrefix,
                    'user_email' => $user->email,
                ]
            ]);

            DB::commit();

            Log::info('✅ Midtrans Snap Token Generated', [
                'order_id' => $orderId,
                'snap_token' => substr($snapToken, 0, 50) . '...',
                'token_length' => strlen($snapToken)
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'client_key' => config('services.midtrans.client_key'),
                'merchant_id' => 'G681396961'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('❌ Midtrans Create Transaction Failed', [
                'transaction_id' => $transaction->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'snap_token' => null,
                'order_id' => null
            ];
        }
    }

    /**
     * Handle Midtrans notification (webhook)
     */
    public function handleNotification(array $notificationData = null)
    {
        // Bungkus semua logika dalam try-catch untuk menangani kegagalan transaksi DB
        try {
            // Memulai Database Transaction
            $result = DB::transaction(function () use ($notificationData) {

                Log::info('🔄 Starting Midtrans Notification Processing', [
                    'notification_data' => $notificationData
                ]);

                // Gunakan data yang diberikan atau buat dari POST global
                if ($notificationData) {
                    $notification = (object) $notificationData;
                } else {
                    $rawPostInput = file_get_contents('php://input');
                    $notification = json_decode($rawPostInput);
                    
                    if (!$notification) {
                        $notification = (object) $_POST;
                    }
                }

                // Validasi data notifikasi
                if (!isset($notification->order_id)) {
                    Log::error('❌ Invalid notification: missing order_id');
                    // Lempar exception untuk membatalkan transaksi DB
                    throw new \Exception('Invalid notification data: missing order_id');
                }

                $orderId = $notification->order_id;
                $transactionStatus = $notification->transaction_status;
                $fraudStatus = $notification->fraud_status ?? null;
                $signatureKey = $notification->signature_key ?? '';

                Log::info("🔍 Processing notification for order: {$orderId}", [
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus,
                ]);

                // Validasi signature key (optional tapi recommended)
                if (!$this->validateSignature(
                    $orderId, 
                    $notification->status_code ?? '200', 
                    $notification->gross_amount ?? '0', 
                    $signatureKey
                )) {
                    Log::warning('⚠️ Signature validation failed', ['order_id' => $orderId]);
                    // throw new \Exception('Invalid signature key'); // Bisa dilempar jika ingin ketat
                }

                // --- PENTING ---
                // Cari transaction berdasarkan order_id DAN kunci barisnya
                $transaction = Transaction::where('order_id', $orderId)
                    // Eager load relasi User, Tryout, dan Bundle
                    ->with(['user', 'tryout', 'bundle']) 
                    ->lockForUpdate() // Kunci baris ini selama transaksi
                    ->first();
                    
                if (!$transaction) {
                    Log::error("❌ Transaction not found for order: {$orderId}");
                    // Lempar exception untuk membatalkan transaksi DB
                    throw new \Exception('Transaction not found in database');
                }

                Log::info("📊 Status Update Attempt", [
                    'order_id' => $orderId,
                    'current_status' => $transaction->status,
                    'new_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus
                ]);

                // --- LOGIKA UPDATE STATUS ---
                $statusMap = [
                    'capture' => $fraudStatus == self::FRAUD_ACCEPT ? Transaction::STATUS_SETTLEMENT : Transaction::STATUS_PENDING,
                    'settlement' => Transaction::STATUS_SETTLEMENT,
                    'pending' => Transaction::STATUS_PENDING,
                    'deny' => Transaction::STATUS_DENY,
                    'expire' => Transaction::STATUS_EXPIRE,
                    'cancel' => Transaction::STATUS_CANCEL,
                ];

                $shouldUpdate = false;

                if (isset($statusMap[$transactionStatus])) {
                    $newStatus = $statusMap[$transactionStatus];
                    
                    if ($transaction->status !== $newStatus) {
                        $transaction->status = $newStatus;
                        $shouldUpdate = true;
                        Log::info("🔄 Status changed: {$transaction->status} → {$newStatus}");
                    }
                    
                    // Update settlement time jika status settlement
                    if ($transactionStatus == 'settlement') {
                        
                        // Cek apakah settlement_time sudah diisi, 
                        // agar grantAccess tidak jalan 2x jika notifikasi dobel
                        if (is_null($transaction->settlement_time) || $transaction->status !== Transaction::STATUS_SETTLEMENT) {
                            
                            $transaction->settlement_time = $notification->settlement_time ?? now();
                            $transaction->fraud_status = self::FRAUD_ACCEPT;
                            $shouldUpdate = true;
                            
                            // Berikan akses item (universal: Tryout/Bundle)
                            $this->grantAccess($transaction); 
                        }
                    }

                    // Update data tambahan
                    if (isset($notification->transaction_id) && $notification->transaction_id != $transaction->transaction_id) {
                        $transaction->transaction_id = $notification->transaction_id;
                        $shouldUpdate = true;
                    }

                    if (isset($notification->payment_type) && $notification->payment_type != $transaction->payment_type) {
                        $transaction->payment_type = $notification->payment_type;
                        $shouldUpdate = true;
                    }

                    if (isset($notification->transaction_time)) {
                        try {
                            $transaction->transaction_time = \Carbon\Carbon::parse($notification->transaction_time);
                            $shouldUpdate = true;
                        } catch (\Exception $e) {
                            Log::warning('Failed to parse transaction_time', [
                                'transaction_time' => $notification->transaction_time
                            ]);
                        }
                    }

                    // Save hanya jika ada perubahan
                    if ($shouldUpdate) {
                        $transaction->save(); 
                        
                        Log::info("✅ Transaction {$orderId} updated successfully", [
                            'new_status' => $transaction->status,
                            'fraud_status' => $transaction->fraud_status,
                            'settlement_time' => $transaction->settlement_time
                        ]);
                    } else {
                        Log::info("ℹ️ No changes needed for transaction {$orderId} (Status already {$transaction->status})");
                    }

                } else {
                    Log::warning("⚠️ Unknown transaction status: {$transactionStatus}", [
                        'order_id' => $orderId
                    ]);
                }

                // Kembalikan data sukses dari dalam closure transaksi
                return [
                    'success' => true, 
                    'transaction' => $transaction,
                    'updated' => $shouldUpdate
                ];

            }); // --- Akhir dari DB::transaction ---

            // Jika transaksi DB berhasil, kembalikan hasilnya
            return $result;

        } catch (\Exception $e) {
            // Jika DB::transaction gagal (karena exception)
            Log::error('💥 ERROR in handleNotification (Transaction Rollback): ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            return [
                'success' => false, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }
    
    /**
     * Grant access to the purchased item (Tryout or Bundle) after successful payment.
     * Dibuat public agar bisa dipanggil dari Livewire/Controller untuk pembelian gratis.
     */
    public function grantAccess(Transaction $transaction): void 
    {
        try {
            // PENTING: Eager load relasi User, Tryout, dan Bundle
            $transaction->load(['user', 'tryout', 'bundle']); 
            
            $user = $transaction->user;
            $item = $transaction->tryout ?? $transaction->bundle;
            $itemType = $item instanceof Tryout ? 'Tryout' : ($item instanceof Bundle ? 'Bundle' : 'Unknown');

            if (!$user || !$item) {
                throw new \Exception("User or Item ({$itemType}) not found for transaction " . $transaction->order_id);
            }
            
            Log::info("🚀 Attempting to grant access to {$itemType} ID: {$item->id}", [
                'user_id' => $user->id,
                'order_id' => $transaction->order_id
            ]);

            // --- Logika Pemberian Akses Universal ---
            
            if ($item instanceof Tryout) {
                // Tryout Individual: Langsung tambahkan 3 attempt
                $this->attachTryoutsWithAttempts($user, collect([$item]), $transaction);
                
            } elseif ($item instanceof Bundle) {
                // 1. Berikan Akses ke Bundle (tabel pivot 'bundle_user')
                if (!$user->purchasedBundles()->where('bundle_id', $item->id)->exists()) {
                     $user->purchasedBundles()->attach($item->id, [ 
                         'purchased_at' => $transaction->settlement_time ?? now(),
                         'order_id' => $transaction->order_id, // Tambahkan ini lagi jika tabel pivot mendukung
                         'transaction_id' => $transaction->id, // Tambahkan ini lagi jika tabel pivot mendukung
                     ]);
                     Log::info('✅ Bundle Access Granted.');
                } else {
                     Log::warning('ℹ️ User already has Bundle access, skipping attachment.');
                }
                
                // 2. Berikan Akses Tryout yang ada di dalam Bundle (tabel pivot 'user_tryouts')
                $tryoutsInBundle = $item->tryouts;
                $this->attachTryoutsWithAttempts($user, $tryoutsInBundle, $transaction);
            }
            
            // ----------------------------------------

        } catch (\Exception $e) {
            Log::error("❌ Failed to Grant {$itemType} Access: " . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'order_id' => $transaction->order_id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; 
        }
    }

    private function attachTryoutsWithAttempts(User $user, $tryouts, Transaction $transaction): void
    {
        $purchasedAt = $transaction->settlement_time ?? now();
        $recordsToInsert = [];

        foreach ($tryouts as $tryout) {
            // Cek apakah Tryout ini sudah memiliki 3 attempts. 
            // Cek hanya perlu dilakukan sekali per Tryout ID.
            $existingAttempts = $user->purchasedTryouts()
                                     ->where('tryout_id', $tryout->id)
                                     ->count();
            
            if ($existingAttempts >= 3) {
                 Log::warning("⚠️ User already has 3 attempts for Tryout ID: {$tryout->id}, skipping.");
                 continue;
            }

            // Tambahkan baris baru hingga mencapai 3 attempts total.
            $neededAttempts = 3 - $existingAttempts;

            for ($i = 1; $i <= $neededAttempts; $i++) {
                $recordsToInsert[] = [
                    'id_user' => $user->id,
                    'tryout_id' => $tryout->id,
                    'order_id' => $transaction->order_id,
                    'purchased_at' => $purchasedAt,
                    'attempt' => $existingAttempts + $i, // Menyesuaikan nomor attempt
                    'is_completed' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($recordsToInsert)) {
            // Gunakan metode insert untuk kinerja yang lebih baik daripada attach berulang
            DB::table('user_tryouts')->insert($recordsToInsert);

            Log::info("✅ Successfully granted Tryout attempts via 'user_tryouts' table.", [
                'user_id' => $user->id,
                'total_records_inserted' => count($recordsToInsert),
                'order_id' => $transaction->order_id,
            ]);
        }
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getStatus(string $orderId): array
    {
        try {
            Log::info('Checking Transaction Status with Midtrans', ['order_id' => $orderId]);

            $statusResponse = MidtransTransaction::status($orderId);

            // ... (rest of the logic remains the same)
            
            return [
                'success' => true,
                'order_id' => $statusResponse->order_id,
                'transaction_id' => $statusResponse->transaction_id,
                'transaction_status' => $statusResponse->transaction_status,
                'fraud_status' => $statusResponse->fraud_status,
                'payment_type' => $statusResponse->payment_type,
                'transaction_time' => $statusResponse->transaction_time,
                'settlement_time' => $statusResponse->settlement_time,
                'gross_amount' => $statusResponse->gross_amount,
                'currency' => $statusResponse->currency,
                'response' => $statusResponse
            ];

        } catch (Exception $e) {
            Log::error('Midtrans Status Check Failed', [
                'order_id' => $orderId,
                'error_message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to check status: ' . $e->getMessage(),
                'order_id' => $orderId
            ];
        }
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction(string $orderId): array
    {
        try {
            Log::info('Canceling Midtrans Transaction', ['order_id' => $orderId]);

            $cancelResponse = MidtransTransaction::cancel($orderId);

            // Update local transaction record
            $transaction = $this->getTransaction($orderId);
            if ($transaction) {
                $transaction->update([
                    'status' => Transaction::STATUS_CANCEL, // Menggunakan konstan model
                    'metadata' => array_merge(
                        $transaction->metadata ?? [],
                        [
                            'cancelled_at' => now()->toISOString(),
                            'cancellation_response' => $cancelResponse
                        ]
                    )
                ]);
            }

            Log::info('Transaction Cancelled Successfully', [
                'order_id' => $orderId,
                'response' => $cancelResponse
            ]);

            return [
                'success' => true,
                'message' => 'Transaction cancelled successfully',
                'response' => $cancelResponse
            ];

        } catch (Exception $e) {
            Log::error('Midtrans Cancel Transaction Failed', [
                'order_id' => $orderId,
                'error_message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to cancel transaction: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Expire transaction
     */
    public function expireTransaction(string $orderId): array
    {
        try {
            Log::info('Expiring Midtrans Transaction', ['order_id' => $orderId]);

            $expireResponse = MidtransTransaction::expire($orderId);

            // Update local transaction record
            $transaction = $this->getTransaction($orderId);
            if ($transaction) {
                $transaction->update([
                    'status' => Transaction::STATUS_EXPIRE, // Menggunakan konstan model
                    'metadata' => array_merge(
                        $transaction->metadata ?? [],
                        [
                            'expired_at_midtrans' => now()->toISOString(),
                            'expiry_response' => $expireResponse
                        ]
                    )
                ]);
            }

            Log::info('Transaction Expired Successfully', [
                'order_id' => $orderId,
                'response' => $expireResponse
            ]);

            return [
                'success' => true,
                'message' => 'Transaction expired successfully',
                'response' => $expireResponse
            ];

        } catch (Exception $e) {
            Log::error('Midtrans Expire Transaction Failed', [
                'order_id' => $orderId,
                'error_message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to expire transaction: ' . $e->getMessage()
            ];
        }
    }


/**
     * Cek transaksi pending yang sudah lewat dari durasi tertentu (auto-check).
     * Dipanggil via Scheduler/Command.
     */
    public function checkPendingTransactions()
    {
        Log::info('? Starting scheduled check for pending transactions > 1 hour...');

        // 1. Ambil transaksi yang statusnya PENDING dan dibuat lebih dari 1 jam yang lalu
        //    Anda bisa sesuaikan subHour() atau subMinutes(60)
        $pendingTransactions = Transaction::where('status', Transaction::STATUS_PENDING)
            ->where('created_at', '<=', now()->subMinutes(60)) 
            ->get();

        $count = 0;

        foreach ($pendingTransactions as $transaction) {
            try {
                // 2. Cek status langsung ke API Midtrans
                $statusResponse = \Midtrans\Transaction::status($transaction->order_id);
                
                // 3. Konversi object response Midtrans menjadi array
                //    agar bisa diterima oleh fungsi handleNotification() Anda
                $notificationData = json_decode(json_encode($statusResponse), true);

                // 4. Proses update database menggunakan logika yang sudah ada
                $this->handleNotification($notificationData);
                
                $count++;
                
                // Beri jeda sedikit agar tidak terkena Rate Limit API Midtrans jika datanya banyak
                usleep(200000); // 0.2 detik

            } catch (\Exception $e) {
                // Jika transaksi tidak ditemukan di Midtrans (404), mungkin perlu di-expire manual
                if (str_contains($e->getMessage(), '404')) {
                    Log::warning("Transaction {$transaction->order_id} not found in Midtrans, marking as failed locally.");
                    $transaction->update(['status' => Transaction::STATUS_FAILED]);
                } else {
                    Log::error("Failed to auto-check transaction {$transaction->order_id}: " . $e->getMessage());
                }
            }
        }

        Log::info("? Finished scheduled check. Processed {$count} transactions.");
    }

    /**
     * Refund transaction
     */
    public function refundTransaction(string $orderId, int $amount = null, string $reason = ''): array
    {
        try {
            Log::info('Processing Refund for Transaction', [
                'order_id' => $orderId,
                'amount' => $amount,
                'reason' => $reason
            ]);

            $refundParams = [];
            if ($amount) {
                $refundParams['amount'] = $amount;
            }
            if ($reason) {
                $refundParams['reason'] = $reason;
            }

            $refundResponse = MidtransTransaction::refund($orderId, $refundParams);

            // Update local transaction record
            $transaction = $this->getTransaction($orderId);
            if ($transaction) {
                $transaction->update([
                    'status' => Transaction::STATUS_REFUND, // Menggunakan konstan model
                    'metadata' => array_merge(
                        $transaction->metadata ?? [],
                        [
                            'refunded_at' => now()->toISOString(),
                            'refund_amount' => $amount,
                            'refund_reason' => $reason,
                            'refund_response' => $refundResponse
                        ]
                    )
                ]);
            }

            Log::info('Transaction Refunded Successfully', [
                'order_id' => $orderId,
                'response' => $refundResponse
            ]);

            return [
                'success' => true,
                'message' => 'Transaction refunded successfully',
                'response' => $refundResponse
            ];

        } catch (Exception $e) {
            Log::error('Midtrans Refund Transaction Failed', [
                'order_id' => $orderId,
                'amount' => $amount,
                'reason' => $reason,
                'error_message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to refund transaction: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction from database
     */
    private function getTransaction(string $orderId): ?Transaction
    {
        return Transaction::where('order_id', $orderId)->first();
    }

    /**
     * Sanitize product name for Midtrans
     */
    private function sanitizeProductName(string $name): string
    {
        $name = substr($name, 0, 50); // Max 50 chars for Midtrans
        $name = preg_replace('/[^\w\s\-]/', '', $name); // Remove special characters
        return trim($name);
    }

    /**
     * Sanitize customer name
     */
    private function sanitizeName(string $name): string
    {
        $name = substr($name, 0, 255);
        $name = preg_replace('/[^\w\s\-\.]/', '', $name);
        return trim($name);
    }

    /**
     * Sanitize phone number
     */
    private function sanitizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '081234567890';
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) < 10) {
            return '081234567890';
        }

        return $phone;
    }

    /**
     * Validate Midtrans signature (for security)
     */
    public function validateSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        if (empty($signatureKey)) {
            return true;
        }

        $serverKey = config('services.midtrans.server_key');
        
        if (empty($serverKey)) {
            \Log::error('❌ Midtrans server key not configured');
            return false;
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $isValid = hash_equals($expectedSignature, $signatureKey);

        \Log::info('🔐 Signature Validation', [
            'order_id' => $orderId,
            'is_valid' => $isValid,
            'expected' => substr($expectedSignature, 0, 20) . '...',
            'received' => substr($signatureKey, 0, 20) . '...'
        ]);

        return $isValid;
    }

    /**
     * Get supported payment methods
     */
    public function getSupportedPaymentMethods(): array
    {
        return [
            'credit_card',
            'gopay',
            'shopeepay',
            'qris',
            'bank_transfer' => ['bca', 'bni', 'bri', 'mandiri', 'permata', 'other'],
            'echannel',
            'cstore' => ['indomaret', 'alfamart'],
            'akulaku'
        ];
    }

    /**
     * Test Midtrans connection
     */
    public function testConnection(): array
    {
        try {
            $this->initializeMidtrans();
            
            $dummyParams = [
                'transaction_details' => [
                    'order_id' => 'TEST-' . time(),
                    'gross_amount' => 10000,
                ],
                'customer_details' => [
                    'first_name' => 'Test Customer',
                    'email' => 'test@example.com',
                    'phone' => '081234567890',
                ]
            ];

            Log::info('🧪 Testing Midtrans Connection', $dummyParams);

            $snapToken = Snap::getSnapToken($dummyParams);
            
            return [
                'success' => true,
                'message' => 'Midtrans connection is working',
                'environment' => Config::$isProduction ? 'production' : 'sandbox',
                'token_sample' => $snapToken ? substr($snapToken, 0, 30) . '...' : 'No token'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Midtrans connection failed: ' . $e->getMessage(),
                'environment' => Config::$isProduction ? 'production' : 'sandbox'
            ];
        }
    }
}
