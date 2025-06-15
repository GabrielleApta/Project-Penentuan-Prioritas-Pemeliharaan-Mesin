<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenyusutanMesin;
use App\Models\Mesin;

class PenyusutanMesinSeeder extends Seeder
{
    public function run()
    {
        $mesinList = Mesin::all();

        foreach ($mesinList as $mesin) {
            $hargaBeli = $mesin->harga_beli;
            $nilaiSisa = $mesin->nilai_sisa;
            $umurEkonomis = $mesin->umur_ekonomis;

            for ($tahun = 1; $tahun <= $umurEkonomis; $tahun++) {
                $penyusutan = ($hargaBeli - $nilaiSisa) / $umurEkonomis;

                PenyusutanMesin::create([
                    'mesin_id' => $mesin->id,
                    'tahun' => $tahun,
                    'penyusutan' => $penyusutan
                ]);
            }
        }
    }
}
