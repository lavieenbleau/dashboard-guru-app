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
        Schema::table('serials', function (Blueprint $table) {
            if (!Schema::hasColumn('serials', 'max_students_per_class')) {
                $table->integer('max_students_per_class')->default(45)->after('paket');
            }
        });
    }

    public function down(): void
    {
        Schema::table('serials', function (Blueprint $table) {
            if (Schema::hasColumn('serials', 'max_students_per_class')) {
                $table->dropColumn('max_students_per_class');
            }
        });
    }
};
