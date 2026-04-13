<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserTryout extends Model
{
    /**
     * Nama tabel pivot yang terkait dengan model.
     * @var string
     */
    protected $table = 'user_tryouts';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * Pastikan semua kolom pivot yang Anda tambahkan ada di sini.
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user', // <-- DIPERBAIKI: Konsisten dengan skema aplikasi Anda
        'tryout_id',
        'attempt',       // <--- INI YANG KURANG
        'order_id',
        'purchased_at',
        'started_at',    
        'ended_at',      
        'is_completed',  
    ];

    /**
     * Casting tipe data untuk kolom.
     * Kolom yang bersifat waktu (timestamp) harus di-cast ke datetime.
     * @var array<string, string>
     */
    protected $casts = [
        'purchased_at' => 'datetime',
        'started_at'   => 'datetime', 
        'ended_at'     => 'datetime', 
        'is_completed' => 'boolean',  
    ];

    /**
     * Tentukan kunci utama dan matikan auto-increment jika Anda menggunakan relasi Many-to-Many standar.
     * Namun, karena Anda menggunakan $table->id() di migrasi, biarkan default ini.
     */
    // protected $primaryKey = 'id';
    // public $incrementing = true;
    
    // Matikan updated_at dan created_at jika Anda tidak menggunakannya di pivot, 
    // tetapi karena Anda menggunakannya di migrasi, biarkan default (true)
    // public $timestamps = true; 

    // --- Relasi (Opsional) ---
    // Anda dapat mendefinisikan relasi balik ke User dan Tryout
    
    public function user()
    {
        // Pastikan foreign key diatur jika nama kolom bukan konvensi
        return $this->belongsTo(User::class, 'id_user');
    }
    
    public function tryout()
    {
        return $this->belongsTo(Tryout::class);
    }
}
