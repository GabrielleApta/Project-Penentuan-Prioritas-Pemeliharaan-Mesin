<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kerusakan_tahunan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mesin')->onDelete('cascade');
            $table->year('tahun');
            $table->integer('kerusakan_ringan')->default(0);
            $table->integer('kerusakan_parah')->default(0);
            $table->float('downtime_ringan')->default(0); // dalam jam
            $table->float('downtime_parah')->default(0); // dalam jam
            $table->timestamps();

            $table->unique(['mesin_id', 'tahun']); // satu data per mesin per tahun
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kerusakan_tahunan');
    }
};
