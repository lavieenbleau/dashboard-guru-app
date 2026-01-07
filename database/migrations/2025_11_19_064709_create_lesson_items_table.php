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
        Schema::create('lesson_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('theme_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subtheme_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('number')->default(1); // Nomor urut materi
            $table->string('title'); // Judul materi
            $table->text('description')->nullable(); // Deskripsi/konten materi
            $table->string('link')->nullable(); // Link eksternal
            $table->string('embed')->nullable(); // Video embed
            $table->string('attachment')->nullable(); // File attachment
            $table->boolean('is_admin')->default(false); // Materi dari admin atau guru
            $table->json('shared_to_classes')->nullable(); // Kelas yang dibagikan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_items');
    }
};
