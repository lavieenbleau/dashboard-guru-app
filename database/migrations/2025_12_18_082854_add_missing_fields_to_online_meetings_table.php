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
        Schema::table('online_meetings', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('online_meetings', 'mapel_id')) {
                $table->unsignedBigInteger('mapel_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('online_meetings', 'room_id')) {
                $table->string('room_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('online_meetings', 'is_internal')) {
                $table->boolean('is_internal')->default(true)->after('room_id');
            }
            if (!Schema::hasColumn('online_meetings', 'max_participants')) {
                $table->integer('max_participants')->nullable()->after('is_internal');
            }
            if (!Schema::hasColumn('online_meetings', 'participants')) {
                $table->json('participants')->nullable()->after('max_participants');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_meetings', function (Blueprint $table) {
            $table->dropColumn(['mapel_id', 'room_id', 'is_internal', 'max_participants', 'participants']);
        });
    }
};
