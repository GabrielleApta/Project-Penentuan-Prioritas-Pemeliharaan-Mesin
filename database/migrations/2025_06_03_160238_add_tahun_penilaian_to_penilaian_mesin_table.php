<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTahunPenilaianToPenilaianMesinTable extends Migration
{
    public function up()
    {
        Schema::table('penilaian_mesin', function (Blueprint $table) {
            $table->integer('tahun_penilaian')->after('waktu_downtime')->default(date('Y'));
            // Bisa disesuaikan default-nya, jangan nullable supaya validasi lebih aman
        });
    }

    public function down()
    {
        Schema::table('penilaian_mesin', function (Blueprint $table) {
            $table->dropColumn('tahun_penilaian');
        });
    }
}
