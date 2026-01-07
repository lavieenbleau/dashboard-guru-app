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
        Schema::create('post_child_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_comment_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->text('message');
            $table->tinyInteger('is_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_child_comments');
    }
};
