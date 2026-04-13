<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Tambahkan ini jika kamu pakai raw query sebagai alternatif
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah enum dengan Doctrine/DBAL terkadang bermasalah di beberapa versi database.
        // Cara paling aman dan dijamin berhasil untuk update ENUM adalah menggunakan raw statement:
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'admin', 'user') DEFAULT 'user'");
        
        // Catatan: Jika kamu ingin menggunakan cara standar ($table->enum(...)->change()), 
        // pastikan kamu sudah menginstall package: composer require doctrine/dbal
    }

    public function down(): void
    {
        // Kembalikan ke enum awal jika migrasi di-rollback
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') DEFAULT 'user'");
    }
};