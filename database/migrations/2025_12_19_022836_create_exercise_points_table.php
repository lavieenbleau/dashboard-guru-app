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
        Schema::create('exercise_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serial_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('student_id');
            $table->text('answer');
            $table->text('competence_point')->nullable();
            $table->string('exercise_point', 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_points');
    }
};
