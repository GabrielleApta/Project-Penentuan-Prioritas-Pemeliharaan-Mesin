<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryPemeliharaanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history_pemeliharaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('jenis_pemeliharaan', ['Preventive', 'Corrective']);
            $table->text('deskripsi');
            $table->decimal('durasi_jam', 5, 2); // Misalnya 1.50 jam
            $table->string('teknisi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_pemeliharaan');
    }
};
