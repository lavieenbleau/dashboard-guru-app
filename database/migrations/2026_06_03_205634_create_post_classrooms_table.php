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
        Schema::create('post_classrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_id');
            $table->unsignedInteger('classroom_id');
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
            
            // Prevent duplicate entries
            $table->unique(['post_id', 'classroom_id']);
        });

        // Migrate existing data from posts.classroom_id
        \Illuminate\Support\Facades\DB::table('posts')
            ->whereNotNull('classroom_id')
            ->orderBy('id')
            ->chunk(100, function ($posts) {
                $inserts = [];
                foreach ($posts as $post) {
                    $inserts[] = [
                        'post_id' => $post->id,
                        'classroom_id' => $post->classroom_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                \Illuminate\Support\Facades\DB::table('post_classrooms')->insertOrIgnore($inserts);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_classrooms');
    }
};
