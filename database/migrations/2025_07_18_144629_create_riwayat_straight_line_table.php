<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatStraightLineTable extends Migration
{
    public function up()
    {
        Schema::create('riwayat_straight_line', function (Blueprint $table) {
            $table->id();
            $table->string('kode_perhitungan');
            $table->unsignedBigInteger('mesin_id')->nullable();
            $table->integer('tahun');
            $table->decimal('penyusutan', 15, 2);
            $table->decimal('akumulasi_penyusutan', 15, 2);
            $table->decimal('nilai_buku', 15, 2);
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamp('tanggal_generate');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_straight_line');
    }
}
