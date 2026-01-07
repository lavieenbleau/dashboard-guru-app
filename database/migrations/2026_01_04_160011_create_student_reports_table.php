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
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedBigInteger('report_type_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('title');
            $table->text('content');
            $table->json('attachments')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
            $table->foreign('report_type_id')->references('id')->on('report_types')->onDelete('restrict');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
