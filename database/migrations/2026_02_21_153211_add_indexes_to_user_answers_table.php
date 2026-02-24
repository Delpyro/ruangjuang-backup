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
        Schema::table('users_answers', function (Blueprint $table) {
            // Menambahkan Composite Index (disarankan untuk performa updateOrCreate)
            $table->index(['user_tryout_id', 'question_id'], 'ua_user_tryout_question_index');
            
            // Atau jika ingin index sendiri-sendiri (opsional)
            // $table->index('user_tryout_id');
            // $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::table('users_answers', function (Blueprint $table) {
            // Menghapus index jika migration di-rollback
            $table->dropIndex('ua_user_tryout_question_index');
        });
    }
};
