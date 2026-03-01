<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            // morphs akan otomatis membuat 2 kolom: promoable_type (string) & promoable_id (bigint)
            $table->morphs('promoable'); 
            $table->integer('order')->default(0); // Opsional: jika nanti ingin diurutkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};