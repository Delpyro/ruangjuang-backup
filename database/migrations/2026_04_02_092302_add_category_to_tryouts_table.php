<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryouts', function (Blueprint $table) {
            // ✨ BARU: Menambahkan kolom category tipe enum dengan default 'umum'
            // Kita letakkan setelah kolom 'slug' agar posisinya rapi di database
            $table->enum('category', ['umum', 'khusus'])->default('umum')->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('tryouts', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn('category');
        });
    }
};