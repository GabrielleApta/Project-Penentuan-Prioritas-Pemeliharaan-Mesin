<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriMesinSeeder extends Seeder
{
    public function run()
    {
        DB::table('kategori_mesin')->truncate(); // Hapus data lama agar tidak duplikat

        DB::table('kategori_mesin')->insert([
            ['nama_kategori' => 'Mesin Jahit'],
            ['nama_kategori' => 'Mesin Pemintal'],
            ['nama_kategori' => 'Mesin Penenun'],
        ]);
    }
}
