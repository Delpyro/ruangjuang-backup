<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
 
    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_CAPTURE = 'capture';
    const STATUS_SETTLEMENT = 'settlement';
    const STATUS_DENY = 'deny';
    const STATUS_CANCEL = 'cancel';
    const STATUS_EXPIRE = 'expire';
    const STATUS_REFUND = 'refund';
    const STATUS_PARTIAL_REFUND = 'partial_refund';
    const STATUS_AUTHORIZE = 'authorize';
    const STATUS_FAILED = 'failed';

    // Fraud Status Constants
    const FRAUD_ACCEPT = 'accept';
    const FRAUD_CHALLENGE = 'challenge';
    const FRAUD_DENY = 'deny';

    protected $fillable = [
        'id_user',
        'id_tryout',
        'id_bundle', // <-- DITAMBAHKAN
        'transaction_id',
        'order_id',
        'transaction_time',
        'settlement_time',
        'expired_at',
        'amount',
        'payment_method',
        'status',
        'ip_user',
        'payment_type',
        'fraud_status',
        'metadata'
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
        'expired_at' => 'datetime',
        'amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    protected $attributes = [
        'metadata' => '{}',
        'status' => self::STATUS_PENDING,
    ];

    // --- Relationships ---

    /**
     * Relationship dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relationship dengan Tryout
     */
    public function tryout()
    {
        return $this->belongsTo(Tryout::class, 'id_tryout');
    }

    /**
     * Relationship dengan Bundle (BARU)
     */
    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'id_bundle');
    }

    // --- Scopes ---

    /**
     * Scope untuk status pending
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope untuk status success/settlement
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SETTLEMENT);
    }

    /**
     * Scope untuk status failed
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [
            self::STATUS_CANCEL, 
            self::STATUS_DENY, 
            self::STATUS_EXPIRE,
            self::STATUS_FAILED
        ]);
    }

    /**
     * Scope untuk status capture
     */
    public function scopeCapture($query)
    {
        return $query->where('status', self::STATUS_CAPTURE);
    }

    /**
     * Scope untuk status refund
     */
    public function scopeRefund($query)
    {
        return $query->whereIn('status', [
            self::STATUS_REFUND,
            self::STATUS_PARTIAL_REFUND
        ]);
    }

    /**
     * Scope berdasarkan rentang tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk transaksi aktif (belum expired)
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expired_at')
              ->orWhere('expired_at', '>', now());
        });
    }

    /**
     * Scope untuk transaksi expired
     */
    public function scopeExpired($query)
    {
        return $query->where('expired_at', '<=', now());
    }

    // --- Accessors & Helpers ---
    
    /**
     * Mendapatkan item yang dibeli (Tryout atau Bundle)
     */
    public function getItemAttribute()
    {
        if ($this->id_bundle) {
            return $this->bundle;
        }

        if ($this->id_tryout) {
            return $this->tryout;
        }

        return null;
    }

    /**
     * Cek apakah transaksi sukses
     */
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SETTLEMENT;
    }

    /**
     * Cek apakah transaksi pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Cek apakah transaksi failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, [
            self::STATUS_CANCEL,
            self::STATUS_DENY,
            self::STATUS_EXPIRE,
            self::STATUS_FAILED
        ]);
    }

    /**
     * Cek apakah transaksi expired
     */
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Cek apakah transaksi dapat di-refund
     */
    public function canBeRefunded(): bool
    {
        return $this->isSuccess() && 
               !in_array($this->status, [self::STATUS_REFUND, self::STATUS_PARTIAL_REFUND]);
    }

    /**
     * Cek apakah fraud status acceptable
     */
    public function isFraudAccept(): bool
    {
        return $this->fraud_status === self::FRAUD_ACCEPT;
    }

    /**
     * Cek apakah fraud status challenge
     */
    public function isFraudChallenge(): bool
    {
        return $this->fraud_status === self::FRAUD_CHALLENGE;
    }

    /**
     * Cek apakah fraud status deny
     */
    public function isFraudDeny(): bool
    {
        return $this->fraud_status === self::FRAUD_DENY;
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CAPTURE => 'Capture',
            self::STATUS_SETTLEMENT => 'Settlement',
            self::STATUS_DENY => 'Deny',
            self::STATUS_CANCEL => 'Cancel',
            self::STATUS_EXPIRE => 'Expire',
            self::STATUS_REFUND => 'Refund',
            self::STATUS_PARTIAL_REFUND => 'Partial Refund',
            self::STATUS_AUTHORIZE => 'Authorize',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * Get all fraud statuses
     */
    public static function getFraudStatuses(): array
    {
        return [
            self::FRAUD_ACCEPT => 'Accept',
            self::FRAUD_CHALLENGE => 'Challenge',
            self::FRAUD_DENY => 'Deny',
        ];
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get fraud status label
     */
    public function getFraudStatusLabelAttribute(): string
    {
        return self::getFraudStatuses()[$this->fraud_status] ?? $this->fraud_status;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Update metadata
     */
    public function updateMetadata(array $metadata): void
    {
        $currentMetadata = $this->metadata ?? [];
        $this->update([
            'metadata' => array_merge($currentMetadata, $metadata)
        ]);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Set default values
            if (empty($transaction->status)) {
                $transaction->status = self::STATUS_PENDING;
            }
            
            if (empty($transaction->metadata)) {
                $transaction->metadata = [];
            }
        });

        static::updating(function ($transaction) {
            // Cek jika 'status' berubah
            if ($transaction->isDirty('status')) {
                
                // 1. Ambil metadata yang ada
                $metadata = $transaction->metadata ?? [];
                
                // 2. Siapkan data perubahan status
                $statusChanges = $metadata['status_changes'] ?? [];
                $statusChanges[] = [
                    'from' => $transaction->getOriginal('status'),
                    'to' => $transaction->status,
                    'at' => now()->toISOString()
                ];
                
                // 3. Masukkan kembali ke array metadata
                $metadata['status_changes'] = $statusChanges;
                
                // 4. SET ATRIBUT SECARA LANGSUNG
                $transaction->metadata = $metadata;
            }
        });
    }
}