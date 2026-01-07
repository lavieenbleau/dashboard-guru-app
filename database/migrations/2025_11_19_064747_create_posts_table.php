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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serial_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('mapel_id');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('slug', 200);
            $table->text('link')->nullable();
            $table->text('attachment')->nullable();
            $table->text('embed')->nullable();
            $table->text('category')->nullable();
            $table->json('shared_to_classes')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->tinyInteger('is_task')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
