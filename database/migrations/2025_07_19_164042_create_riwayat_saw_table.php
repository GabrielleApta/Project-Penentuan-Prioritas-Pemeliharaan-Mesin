<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatSawTable extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_saw', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin'); // tanpa onDelete cascade
            $table->string('kode_perhitungan')->index(); // bisa di-filter cepat
            $table->string('nama_mesin'); // snapshot nama
            $table->float('akumulasi_penyusutan');
            $table->float('usia_mesin');
            $table->float('frekuensi_kerusakan');
            $table->float('waktu_downtime');
            $table->float('skor_akhir');
            $table->integer('ranking');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_saw');
    }
}
