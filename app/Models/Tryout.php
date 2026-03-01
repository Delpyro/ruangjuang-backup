<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Tryout extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'is_hots',
        'duration',
        'content',
        'quote',
        'price',
        'discount',
        // ✨ BARU: Kolom Tanggal Diskon
        'discount_start_date', 
        'discount_end_date',
        // End BARU
        'is_active',
    ];

    protected $casts = [
        'is_hots' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'integer',
        'discount' => 'integer',
        'duration' => 'integer',
        'deleted_at' => 'datetime',
        // ✨ BARU: Cast kolom tanggal diskon sebagai datetime
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
        // End BARU
    ];

    /**
     * Scope a query to only include active tryouts.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include HOTS tryouts.
     */
    public function scopeHots(Builder $query): void
    {
        $query->where('is_hots', true);
    }

    /**
     * Relationship dengan questions
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'id_tryout');
    }

    /**
     * Relationship dengan questions yang aktif
     */
    public function activeQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'id_tryout')->where('is_active', true);
    }


    public function promo()
    {
        return $this->morphOne(Promo::class, 'promoable');
    }

    /**
     * Relationship dengan user yang telah membeli tryout ini.
     * PERBAIKAN KRITIS: Menggunakan 'id_user' sebagai foreign key di tabel pivot.
     */
    public function purchasers(): BelongsToMany
    {
        // Asumsi tabel pivot adalah 'user_tryouts' atau 'tryout_user'
        // dan kolom foreign key ke User adalah 'id_user'
        return $this->belongsToMany(User::class, 'user_tryouts', 'tryout_id', 'id_user')
            ->using(UserTryout::class) // Jika Anda membuat model untuk pivot table
            ->withPivot('order_id', 'purchased_at')
            ->withTimestamps();
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'bundle_tryout', 'tryout_id', 'bundle_id');
    }

    // --- Accessors (Final Price & Discount) ---

    public function getFinalPriceAttribute(): int
    {
        // Mendapatkan waktu saat ini
        $now = now();
        
        // 1. Cek apakah ada diskon dan diskon > 0
        if (!$this->discount || $this->discount <= 0) {
            return $this->price;
        }

        // 2. Cek validitas periode diskon
        $isDiscountActive = true;

        // Cek Tanggal Mulai (jika ada)
        if ($this->discount_start_date && $now->lessThan($this->discount_start_date)) {
            $isDiscountActive = false;
        }

        // Cek Tanggal Berakhir (jika ada)
        if ($this->discount_end_date && $now->greaterThan($this->discount_end_date)) {
            $isDiscountActive = false;
        }
        
        // Terapkan diskon jika sedang aktif
        if ($isDiscountActive) {
            return max(0, $this->price - $this->discount); // Pastikan harga tidak negatif
        }
        
        // Jika diskon ada tapi tidak aktif karena tanggal, kembalikan harga normal
        return $this->price;
    }

    public function getDiscountPercentageAttribute(): float
    {
        if ($this->discount && $this->discount > 0 && $this->price > 0) {
            return round(($this->discount / $this->price) * 100);
        }
        
        return 0;
    }

    // --- Accessors (Helper & Counts) ---

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration) return '-';
        
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        } else {
            return "{$minutes} menit";
        }
    }

    public function getActiveQuestionsCountAttribute(): int
    {
        return $this->activeQuestions()->count();
    }

    // --- Laravel 9+ Mutators (Overwriting existing accessors/mutators) ---

    protected function finalPrice(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Ambil tanggal dan waktu saat ini
                $now = Carbon::now();
                $discount_start = $attributes['discount_start_date'] ? Carbon::parse($attributes['discount_start_date']) : null;
                $discount_end = $attributes['discount_end_date'] ? Carbon::parse($attributes['discount_end_date']) : null;

                $is_discount_active = $attributes['discount'] > 0
                    && (!$discount_start || $now->greaterThanOrEqualTo($discount_start))
                    && (!$discount_end || $now->lessThanOrEqualTo($discount_end));

                if ($is_discount_active) {
                    return $attributes['price'] - $attributes['discount'];
                }

                return $attributes['price'];
            },
        );
    }

    protected function discountPercentage(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['price'] > 0 && $attributes['discount'] > 0) {
                    return round(($attributes['discount'] / $attributes['price']) * 100);
                }
                return 0;
            },
        );
    }


    public function scopeHasActiveDiscount($query)
    {
        $now = Carbon::now();
        return $query->where('discount', '>', 0)
                     ->where(function ($q) use ($now) {
                         $q->whereNull('discount_start_date')
                          ->orWhere('discount_start_date', '<=', $now);
                     })
                     ->where(function ($q) use ($now) {
                         $q->whereNull('discount_end_date')
                          ->orWhere('discount_end_date', '>=', $now);
                     });
    }
}
