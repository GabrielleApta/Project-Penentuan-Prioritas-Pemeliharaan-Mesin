<?php

namespace App\Imports;

use App\Models\KerusakanTahunan;
use App\Models\Mesin;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KerusakanTahunanImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Cari mesin berdasarkan nama_mesin
            $mesin = Mesin::where('nama_mesin', trim($row['nama_mesin']))->first();

            if (!$mesin) {
                // Skip baris jika mesin tidak ditemukan
                continue;
            }

            // Simpan atau update data kerusakan tahunan
            KerusakanTahunan::updateOrCreate(
                [
                    'mesin_id' => $mesin->id,
                    'tahun'    => (int) $row['tahun']
                ],
                [
                    'kerusakan_ringan' => (int) $row['kerusakan_ringan'],
                    'kerusakan_parah'  => (int) $row['kerusakan_parah'],
                    'downtime_ringan'  => (float) str_replace(',', '.', $row['downtime_ringan']),
                    'downtime_parah'   => (float) str_replace(',', '.', $row['downtime_parah']),
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            '*.nama_mesin'        => 'required|string|exists:mesin,nama_mesin',
            '*.tahun'             => 'required|integer|min:2000|max:' . date('Y'),
            '*.kerusakan_ringan'  => 'required|integer|min:0',
            '*.kerusakan_parah'   => 'required|integer|min:0',
            '*.downtime_ringan'   => 'required|numeric|min:0',
            '*.downtime_parah'    => 'required|numeric|min:0',
        ];
    }
}
