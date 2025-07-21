<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNormalisasiColumnsToRiwayatSawTable extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_saw', function (Blueprint $table) {
            $table->float('norm_akumulasi_penyusutan')->nullable();
            $table->float('norm_usia_mesin')->nullable();
            $table->float('norm_frekuensi_kerusakan')->nullable();
            $table->float('norm_waktu_downtime')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_saw', function (Blueprint $table) {
            $table->dropColumn([
                'norm_akumulasi_penyusutan',
                'norm_usia_mesin',
                'norm_frekuensi_kerusakan',
                'norm_waktu_downtime',
            ]);
        });
    }
}
