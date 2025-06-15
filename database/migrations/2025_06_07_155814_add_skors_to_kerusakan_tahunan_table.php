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
        Schema::table('kerusakan_tahunan', function (Blueprint $table) {
            //
            $table->float('skor_frekuensi_kerusakan')->nullable()->after('kerusakan_parah');
        $table->float('skor_waktu_downtime')->nullable()->after('downtime_parah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerusakan_tahunan', function (Blueprint $table) {
            //
             $table->dropColumn(['skor_frekuensi_kerusakan', 'skor_waktu_downtime']);
        });
    }
};
