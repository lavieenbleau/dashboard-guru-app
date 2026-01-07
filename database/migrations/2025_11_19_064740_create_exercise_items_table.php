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
        Schema::create('exercise_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('competence_id')->nullable();
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('exercise_type_id');
            $table->unsignedBigInteger('exercise_model_id');
            $table->tinyInteger('exercise_choice');
            $table->integer('exercise_number');
            $table->text('question');
            $table->text('selection')->nullable();
            $table->text('answer')->nullable();
            $table->tinyInteger('is_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_items');
    }
};
