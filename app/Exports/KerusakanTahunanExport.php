<?php

namespace App\Exports;

use App\Models\KerusakanTahunan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KerusakanTahunanExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return KerusakanTahunan::with('mesin')->orderBy('tahun')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Mesin',
            'Tahun',
            'Kerusakan Ringan',
            'Downtime Ringan (jam)',
            'Kerusakan Parah',
            'Downtime Parah (jam)',
        ];
    }

    public function map($row): array
    {
        static $i = 1;

        return [
            $i++,
            $row->mesin->nama_mesin ?? '-',
            $row->tahun,
            $row->kerusakan_ringan,
            $row->downtime_ringan,
            $row->kerusakan_parah,
            $row->downtime_parah,
        ];
    }
}
