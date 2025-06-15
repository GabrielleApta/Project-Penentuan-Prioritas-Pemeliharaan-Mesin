<?php

namespace App\Exports;

use App\Models\HasilSaw;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return HasilSaw::with('mesin')->orderBy('ranking', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'Nama Mesin' => $item->mesin->nama_mesin,
                    'Skor Akhir' => $item->skor_akhir,
                    'Ranking' => $item->ranking
                ];
            });
    }

    public function headings(): array
    {
        return ['Nama Mesin', 'Skor Akhir', 'Ranking'];
    }
}
