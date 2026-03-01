<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = ['promoable_type', 'promoable_id', 'order'];

    // Relasi Polimorfik
    public function promoable()
    {
        return $this->morphTo();
    }
}