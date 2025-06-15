<?php

namespace App\Exports;

use App\Models\Mesin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MesinExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Mesin::select('nama_mesin', 'kode_mesin', 'tahun_pembelian', 'spesifikasi_mesin', 'daya_motor', 'lokasi_mesin')->get();
    }

    public function headings(): array
    {
        return ['Nama Mesin', 'Kode Mesin', 'Tahun Pembelian', 'Spesifikasi Mesin', 'Daya Motor', 'Lokasi Mesin'];
    }
}
