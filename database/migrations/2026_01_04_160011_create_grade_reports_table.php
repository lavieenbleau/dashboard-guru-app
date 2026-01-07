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
        Schema::create('grade_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('classroom_id');
            $table->tinyInteger('semester');
            $table->string('academic_year', 20);
            $table->decimal('attendance_score', 5, 2)->default(0);
            $table->decimal('assignment_score', 5, 2)->default(0);
            $table->decimal('quiz_score', 5, 2)->default(0);
            $table->decimal('uh_score', 5, 2)->default(0);
            $table->decimal('pts_score', 5, 2)->default(0);
            $table->decimal('pas_score', 5, 2)->default(0);
            $table->decimal('final_score', 5, 2)->default(0);
            $table->string('grade', 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapels')->onDelete('cascade');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
            
            $table->unique(['student_id', 'mapel_id', 'semester', 'academic_year'], 'grade_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_reports');
    }
};
