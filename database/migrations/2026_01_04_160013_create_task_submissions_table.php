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
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->dateTime('submitted_at');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->enum('status', ['submitted', 'graded', 'late'])->default('submitted');
            $table->timestamps();
            
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['task_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
