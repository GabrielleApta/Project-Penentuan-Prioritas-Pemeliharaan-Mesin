<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penilaian_mesin', function (Blueprint $table) {
            // Step 1: Drop foreign key constraint terlebih dahulu
            $table->dropForeign(['mesin_id']);

            // Step 2: Drop unique index yang dibuat oleh migration lama
            $table->dropUnique('penilaian_mesin_mesin_id_unique');

            // Step 3: Tambahkan unique composite key yang baru
            $table->unique(['mesin_id', 'tahun_penilaian']);

            // Step 4: Tambahkan kembali foreign key constraint
            $table->foreign('mesin_id')->references('id')->on('mesin')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian_mesin', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
            $table->dropUnique(['mesin_id', 'tahun_penilaian']);

            $table->unique('mesin_id');
            $table->foreign('mesin_id')->references('id')->on('mesin')->onDelete('cascade');
        });
    }
};
