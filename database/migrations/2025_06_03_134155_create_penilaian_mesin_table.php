<?php

// database/migrations/xxxx_xx_xx_create_penilaian_mesin_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penilaian_mesin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin')->onDelete('cascade');
            $table->float('akumulasi_penyusutan')->default(0);
            $table->integer('usia_mesin')->default(0);
            $table->float('frekuensi_kerusakan')->default(0);
            $table->float('waktu_downtime')->default(0);
            $table->timestamps();

            $table->unique('mesin_id'); // supaya 1 mesin hanya punya 1 penilaian
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_mesin');
    }
};
