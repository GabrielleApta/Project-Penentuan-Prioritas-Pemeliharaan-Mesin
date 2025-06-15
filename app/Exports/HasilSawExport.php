<?php

namespace App\Exports;

use App\Models\HasilSaw;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HasilSawExport implements FromCollection, WithHeadings, WithMapping
{
    protected $mesin_id;

    /**
     * Konstruktor untuk menampung mesin_id jika ada
     */
    public function __construct($mesin_id = null)
    {
        $this->mesin_id = $mesin_id;
    }

    /**
     * Ambil data hasil SAW dari database
     */
    public function collection()
    {
        $query = HasilSaw::with('mesin')->orderBy('rangking', 'asc');

        if ($this->mesin_id) {
            $query->where('mesin_id', $this->mesin_id);
        }

        return $query->get();
    }

    /**
     * Header kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Mesin',
            'Skor Akhir',
            'Ranking',
            'Tanggal Perhitungan'
        ];
    }

    /**
     * Format setiap baris data dalam file Excel
     */
    public function map($hasil): array
    {
        return [
            $hasil->id,
            $hasil->mesin->nama_mesin ?? 'Tidak Diketahui',
            number_format($hasil->skor_akhir, 2), // Format angka 2 desimal
            $hasil->rangking,
            $hasil->created_at->format('Y-m-d H:i:s')
        ];
    }
}
