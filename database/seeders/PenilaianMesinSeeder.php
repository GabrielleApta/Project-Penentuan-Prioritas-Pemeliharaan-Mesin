<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenilaianMesin;
use App\Models\Mesin;
use App\Models\Kriteria;

class PenilaianMesinSeeder extends Seeder
{
    public function run()
    {
        $mesin1 = Mesin::where('kode_mesin', 'MPA001')->first();
        $mesin2 = Mesin::where('kode_mesin', 'MPB002')->first();

        $kriteria = Kriteria::all();

        foreach ($kriteria as $krit) {
            PenilaianMesin::create([
                'mesin_id' => $mesin1->id,
                'kriteria_id' => $krit->id,
                'nilai' => rand(1, 10) // Nilai acak untuk contoh
            ]);

            PenilaianMesin::create([
                'mesin_id' => $mesin2->id,
                'kriteria_id' => $krit->id,
                'nilai' => rand(1, 10) // Nilai acak untuk contoh
            ]);
        }
    }
}
