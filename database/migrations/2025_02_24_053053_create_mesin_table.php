<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesinTable extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up()
    {
        if (!Schema::hasTable('mesin')) {
            Schema::create('mesin', function (Blueprint $table) {
                $table->id();
                $table->string('nama_mesin');
                $table->string('kode_mesin')->unique(); // Tambahkan unik
                $table->string('kategori_id')->unique();
                $table->decimal('harga_beli', 15, 2);
                $table->unsignedInteger('tahun_pembelian'); // Tambahkan unsigned
                $table->text('spesifikasi_mesin'); // Menggunakan text untuk deskripsi panjang
                $table->decimal('daya_motor', 15, 2);
                $table->string('lokasi_mesin');
                $table->decimal('nilai_sisa', 15, 2);
                $table->unsignedInteger('umur_ekonomis'); // Tambahkan unsigned
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse migration.
     */
    public function down()
    {
        Schema::dropIfExists('mesin');
    }
}
