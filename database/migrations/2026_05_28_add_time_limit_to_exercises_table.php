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
        Schema::table('exercises', function (Blueprint $table) {
            // Tambah field time_limit setelah title
            $table->unsignedSmallInteger('time_limit')->nullable()->default(null)->comment('Waktu pengerjaan dalam menit. NULL = tidak ada batas waktu')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn('time_limit');
        });
    }
};
