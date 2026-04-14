<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // ✨ Ini akan menambahkan kolom deleted_at bertipe timestamp
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // ✨ Ini akan menghapus kolom deleted_at jika kamu melakukan rollback
            $table->dropSoftDeletes(); 
        });
    }
};