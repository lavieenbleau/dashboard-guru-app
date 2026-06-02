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
        Schema::table('share_exercises', function (Blueprint $table) {
            $table->unsignedBigInteger('classroom_id')->nullable()->after('exercise_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('share_exercises', function (Blueprint $table) {
            $table->dropColumn('classroom_id');
        });
    }
};
