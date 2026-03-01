<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bundle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'discount',
        'is_active',
        'expired_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
        'discount' => 'integer',
        'expired_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope untuk bundle aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk bundle yang belum expired
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expired_at')
              ->orWhere('expired_at', '>', now());
        });
    }

    /**
     * Scope untuk bundle yang available (aktif + belum expired)
     */
    public function scopeAvailable($query)
    {
        return $query->active()->notExpired();
    }

    /**
     * Hubungan many-to-many dengan Tryout.
     */
    public function tryouts(): BelongsToMany
    {
        return $this->belongsToMany(Tryout::class, 'bundle_tryout', 'bundle_id', 'tryout_id');
    }


    public function promo()
    {
        return $this->morphOne(Promo::class, 'promoable');
    }
    /**
     * User yang telah membeli bundle ini. (Relasi Pivot Resmi)
     * Relasi ini digunakan untuk menandai kepemilikan di tabel pivot 'bundle_user'.
     */
    public function purchasers(): BelongsToMany
    {
        // PERBAIKAN KRITIS: Menggunakan 'id_user' sebagai foreign key Model User
        // Asumsi tabel pivot adalah 'bundle_user'
        return $this->belongsToMany(User::class, 'bundle_user', 'bundle_id', 'id_user')
            ->withPivot(['order_id', 'purchased_at', 'transaction_id']) 
            ->withTimestamps();
    }
    
    /**
     * User yang telah membeli bundle ini (Melihat via Tabel Transaksi).
     * Ini adalah versi lama yang dipertahankan untuk referensi data historis di tabel transactions.
     */
    public function purchasersByTransactions()
    {
        // FIX: Menggunakan 'id_user' di where clause
        return $this->belongsToMany(User::class, 'transactions', 'id_bundle', 'id_user')
                    ->whereIn('transactions.status', ['settlement', 'paid']);
    }

    /**
     * Transaksi yang berkaitan dengan bundle ini.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_bundle'); // Pastikan foreign key benar
    }
    
    /**
     * Hitung harga akhir setelah diskon.
     */
    public function getFinalPriceAttribute(): int
    {
        // Menggunakan max(0, ...) untuk memastikan harga tidak negatif
        return max(0, $this->price - $this->discount);
    }

    /**
     * Hitung persentase diskon.
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->price > 0 && $this->discount > 0) {
            return round(($this->discount / $this->price) * 100, 2);
        }
        return 0;
    }

    /**
     * Hitung total harga tryout individual
     */
    public function getTotalTryoutPriceAttribute(): int
    {
        // Pastikan relasi tryouts sudah dimuat jika dipanggil di luar model
        return $this->tryouts->sum('price');
    }

    /**
     * Hitung berapa persen hemat dibanding beli individual
     */
    public function getSavingsPercentageAttribute(): float
    {
        $totalIndividualPrice = $this->total_tryout_price;
        if ($totalIndividualPrice > 0) {
            $savings = $totalIndividualPrice - $this->final_price;
            return round(($savings / $totalIndividualPrice) * 100, 2);
        }
        return 0;
    }

    /**
     * Cek apakah bundle expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Cek apakah bundle available untuk dibeli
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && !$this->is_expired;
    }

    /**
     * Hitung sisa waktu hingga expired (dalam hari)
     */
    public function getDaysUntilExpiredAttribute(): ?int
    {
        if (!$this->expired_at) {
            return null;
        }
        
        return now()->diffInDays($this->expired_at, false);
    }

    public function getRouteKeyName(): string
    {
        return 'slug'; // Laravel akan mencari Bundle berdasarkan kolom 'slug'
    } 
}
