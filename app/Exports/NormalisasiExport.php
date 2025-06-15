<?php

namespace App\Exports;

use App\Models\PenilaianMesin;
use App\Models\Mesin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NormalisasiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $data = PenilaianMesin::with('mesin')->get();

        // ambil min tiap kriteria karena semuanya cost
        $minPenyusutan = $data->min('akumulasi_penyusutan');
        $minUsia = $data->min('usia_mesin');
        $minFrekuensi = $data->min('frekuensi_kerusakan');
        $minDowntime = $data->min('waktu_downtime');

        $bobot = [
            'penyusutan' => 0.30,
            'usia' => 0.30,
            'frekuensi' => 0.20,
            'downtime' => 0.20,
        ];

        return $data->map(function ($item) use ($minPenyusutan, $minUsia, $minFrekuensi, $minDowntime, $bobot) {
            $normPenyusutan = $minPenyusutan / $item->akumulasi_penyusutan;
            $normUsia = $minUsia / $item->usia_mesin;
            $normFrekuensi = $minFrekuensi / $item->frekuensi_kerusakan;
            $normDowntime = $minDowntime / $item->waktu_downtime;

            $skorAkhir =
                $normPenyusutan * $bobot['penyusutan'] +
                $normUsia * $bobot['usia'] +
                $normFrekuensi * $bobot['frekuensi'] +
                $normDowntime * $bobot['downtime'];

            return [
                'Nama Mesin' => $item->mesin->nama_mesin,
                'Norm. Penyusutan' => round($normPenyusutan, 4),
                'Norm. Usia' => round($normUsia, 4),
                'Norm. Frekuensi' => round($normFrekuensi, 4),
                'Norm. Downtime' => round($normDowntime, 4),
                'Skor Akhir' => round($skorAkhir, 4),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Mesin',
            'Norm. Penyusutan',
            'Norm. Usia',
            'Norm. Frekuensi',
            'Norm. Downtime',
            'Skor Akhir',
        ];
    }
}
