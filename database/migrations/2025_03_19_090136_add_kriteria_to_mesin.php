<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('mesin', function (Blueprint $table) {
        $table->integer('usia_mesin')->after('nilai_sisa');
        $table->integer('frekuensi_kerusakan')->after('usia_mesin');
        $table->float('downtime_mesin')->after('frekuensi_kerusakan');
        $table->integer('ketersediaan_suku_cadang')->after('downtime_mesin');
        $table->float('dampak_produksi')->after('ketersediaan_suku_cadang');
    });
}

public function down()
{
    Schema::table('mesin', function (Blueprint $table) {
        if (Schema::hasColumn('mesin', 'usia_mesin')) {
            $table->dropColumn('usia_mesin');
        }
        if (Schema::hasColumn('mesin', 'frekuensi_kerusakan')) {
            $table->dropColumn('frekuensi_kerusakan');
        }
        if (Schema::hasColumn('mesin', 'downtime_mesin')) {
            $table->dropColumn('downtime_mesin');
        }
        if (Schema::hasColumn('mesin', 'ketersediaan_suku_cadang')) {
            $table->dropColumn('ketersediaan_suku_cadang');
        }
        if (Schema::hasColumn('mesin', 'dampak_produksi')) {
            $table->dropColumn('dampak_produksi');
        }
    });
}


};
