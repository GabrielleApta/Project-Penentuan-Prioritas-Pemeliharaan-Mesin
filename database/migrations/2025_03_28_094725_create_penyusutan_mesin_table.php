<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('penyusutan_mesin', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('mesin_id')->constrained('mesin')->onDelete('cascade'); // Foreign Key ke tabel mesin
            $table->year('tahun'); // Kolom tahun (format YYYY)
            $table->decimal('penyusutan', 15, 2)->default(0); // Nilai penyusutan (desimal)
            $table->double('akumulasi_penyusutan')->default(0);
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyusutan_mesin');
    }
};
