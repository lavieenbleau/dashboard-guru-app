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
        Schema::table('posts', function (Blueprint $table) {
            $table->text('quiz_data')->nullable(); // Store questions as JSON
            $table->integer('time_limit')->nullable(); // Time limit in minutes
            $table->tinyInteger('is_quiz')->default(0); // 0=materi, 1=task, 2=quiz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['quiz_data', 'time_limit', 'is_quiz']);
        });
    }
};
