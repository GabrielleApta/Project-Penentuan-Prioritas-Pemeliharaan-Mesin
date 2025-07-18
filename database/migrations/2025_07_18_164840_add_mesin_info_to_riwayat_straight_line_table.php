<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMesinInfoToRiwayatStraightLineTable extends Migration
{
    public function up()
    {
        Schema::table('riwayat_straight_line', function (Blueprint $table) {
            $table->string('nama_mesin')->nullable()->after('mesin_id');
            $table->double('harga_beli')->default(0)->after('nama_mesin');
            $table->double('nilai_sisa')->default(0)->after('harga_beli');
            $table->integer('umur_ekonomis')->default(0)->after('nilai_sisa');
        });
    }

    public function down()
    {
        Schema::table('riwayat_straight_line', function (Blueprint $table) {
            $table->dropColumn(['nama_mesin', 'harga_beli', 'nilai_sisa', 'umur_ekonomis']);
        });
    }
}
