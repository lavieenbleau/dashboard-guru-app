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
        Schema::create('online_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Guru yang membuat
            $table->foreignId('mapel_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title'); // Judul meeting
            $table->text('description')->nullable(); // Deskripsi meeting
            $table->string('meeting_code')->nullable()->unique(); // Kode unik untuk join
            $table->string('meeting_link')->nullable(); // Link meeting (untuk platform eksternal)
            $table->string('platform')->default('jitsi'); // jitsi, zoom, gmeet, etc
            $table->dateTime('start_time'); // Waktu mulai
            $table->dateTime('end_time'); // Waktu selesai
            $table->enum('status', ['scheduled', 'ongoing', 'ended', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_meetings');
    }
};
