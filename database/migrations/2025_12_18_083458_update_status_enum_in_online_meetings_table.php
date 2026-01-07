<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any invalid status values to 'scheduled'
        DB::statement("UPDATE `online_meetings` SET `status` = 'scheduled' WHERE `status` NOT IN ('scheduled', 'ended', 'cancelled')");
        
        // Then modify the ENUM column to include 'ongoing'
        DB::statement("ALTER TABLE `online_meetings` MODIFY COLUMN `status` ENUM('scheduled', 'ongoing', 'ended', 'cancelled') NOT NULL DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `online_meetings` MODIFY COLUMN `status` ENUM('scheduled', 'ended', 'cancelled') NOT NULL DEFAULT 'scheduled'");
    }
};
