<?php

namespace App\Exports;

use App\Models\Depresiasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepresiasiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Depresiasi::with('mesin')->get()->map(function ($item) {
            return [
                'Nama Mesin' => $item->mesin->nama_mesin,
                'Kode Mesin' => $item->mesin->kode_mesin,
                'Tahun' => $item->tahun,
                'Penyusutan' => $item->penyusutan,
                'Nilai Buku' => $item->nilai_buku,
                'Akumulasi Penyusutan' => $item->akumulasi_penyusutan
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Mesin', 'Kode Mesin', 'Tahun',
            'Penyusutan', 'Nilai Buku', 'Akumulasi Penyusutan'
        ];
    }
}
