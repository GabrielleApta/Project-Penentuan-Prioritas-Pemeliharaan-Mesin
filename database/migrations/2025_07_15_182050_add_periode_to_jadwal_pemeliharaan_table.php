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
        Schema::table('jadwal_pemeliharaan', function (Blueprint $table) {
            //
            $table->string('periode')->after('mesin_id')->nullable(); // contoh: 2025-07
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pemeliharaan', function (Blueprint $table) {
            //
            $table->dropColumn('periode');
        });
    }
};
