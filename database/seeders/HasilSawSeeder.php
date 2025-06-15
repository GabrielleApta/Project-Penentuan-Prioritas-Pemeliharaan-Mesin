<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HasilSaw;
use App\Models\Mesin;

class HasilSawSeeder extends Seeder
{
    public function run()
    {
        $mesin1 = Mesin::where('kode_mesin', 'MPA001')->first();
        $mesin2 = Mesin::where('kode_mesin', 'MPB002')->first();

        HasilSaw::create([
            'mesin_id' => $mesin1->id,
            'skor_akhir' => 85.5, // Contoh skor
            'ranking' => 1
        ]);

        HasilSaw::create([
            'mesin_id' => $mesin2->id,
            'skor_akhir' => 78.2, // Contoh skor
            'ranking' => 2
        ]);
    }
}
