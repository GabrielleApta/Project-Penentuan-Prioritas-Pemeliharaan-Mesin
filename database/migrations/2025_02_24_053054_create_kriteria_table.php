<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKriteriaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('kriteria')) {
            Schema::create('kriteria', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kriteria');
                $table->decimal('bobot', 5, 2);
                $table->enum('jenis_kriteria', ['benefit', 'cost']);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('kriteria');
    }
}
