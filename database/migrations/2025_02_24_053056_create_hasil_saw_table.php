<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilSawTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('hasil_saw')) {
            Schema::create('hasil_saw', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mesin_id');
                $table->decimal('skor_akhir', 10, 4);
                $table->integer('rangking');
                $table->timestamps();

                // Foreign key constraint
                $table->foreignId('mesin_id')->constrained('mesin')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('hasil_saw');
    }
}
