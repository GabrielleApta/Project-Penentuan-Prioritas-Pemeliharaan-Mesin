<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kriteria;

class KriteriaSeeder extends Seeder
{
    public function run()
    {
        Kriteria::create([
            'nama_kriteria' => 'Usia Mesin',
            'bobot' => 0.3
        ]);

        Kriteria::create([
            'nama_kriteria' => 'Frekuensi Kerusakan',
            'bobot' => 0.4
        ]);

        Kriteria::create([
            'nama_kriteria' => 'Biaya Perawatan',
            'bobot' => 0.3
        ]);
    }
}
