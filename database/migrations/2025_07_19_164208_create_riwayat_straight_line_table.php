<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatStraightLineTable extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_straight_line', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin');
            $table->string('kode_perhitungan')->index();
            $table->string('nama_mesin');
            $table->year('tahun_pembelian');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('nilai_sisa', 15, 2);
            $table->unsignedTinyInteger('umur_ekonomis');
            $table->unsignedTinyInteger('usia_mesin');
            $table->decimal('penyusutan_per_tahun', 15, 2);
            $table->decimal('akumulasi_penyusutan', 15, 2);
            $table->decimal('nilai_buku', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_straight_line');
    }
}
