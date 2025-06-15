<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mesin;

class MesinSeeder extends Seeder
{
    public function run()
    {
        Mesin::create([
            'nama_mesin' => 'Mesin Pemintal A',
            'kode_mesin' => 'MPA001',
            'harga_beli' => 100000000,
            'nilai_sisa' => 10000000,
            'umur_ekonomis' => 10
        ]);

        Mesin::create([
            'nama_mesin' => 'Mesin Pemintal B',
            'kode_mesin' => 'MPB002',
            'harga_beli' => 120000000,
            'nilai_sisa' => 15000000,
            'umur_ekonomis' => 12
        ]);
    }
}
