<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin')->onDelete('cascade');
            $table->year('tahun'); // tahun penilaian, misalnya 2024
            $table->float('nilai_penyusutan')->default(0);
            $table->float('nilai_usia')->default(0);
            $table->float('nilai_frekuensi')->default(0);
            $table->float('nilai_downtime')->default(0);
            $table->timestamps();

            $table->unique(['mesin_id', 'tahun']); // agar 1 mesin hanya dinilai 1x per tahun
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
