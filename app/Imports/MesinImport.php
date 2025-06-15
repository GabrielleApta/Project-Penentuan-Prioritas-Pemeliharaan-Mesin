<?php

namespace App\Imports;

use App\Models\Mesin;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class MesinImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Normalisasi nilai
            $hargaBeli = preg_replace('/[^\d]/', '', $row['harga_beli']); // buang Rp, titik, koma
            $dayaMotor = str_replace(',', '.', $row['daya_motor']); // koma jadi titik desimal

            // Gunakan default jika nilai kosong
            $nilaiSisa      = isset($row['nilai_sisa']) ? preg_replace('/[^\d]/', '', $row['nilai_sisa']) : 0;
            $umurEkonomis   = isset($row['umur_ekonomis']) ? (int) $row['umur_ekonomis'] : 0;
            $status         = $row['status'] ?? 'aktif';

            Mesin::updateOrCreate(
                ['kode_mesin' => $row['kode_mesin']],
                [
                    'nama_mesin'        => $row['nama_mesin'],
                    'harga_beli'        => (int) $hargaBeli,
                    'tahun_pembelian'   => (int) $row['tahun_pembelian'],
                    'spesifikasi_mesin' => $row['spesifikasi_mesin'],
                    'daya_motor'        => (float) $dayaMotor,
                    'lokasi_mesin'      => $row['lokasi_mesin'],
                    'nilai_sisa'        => (int) $nilaiSisa,
                    'umur_ekonomis'     => (int) $umurEkonomis,
                    'status'            => strtolower($status),
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            '*.nama_mesin'        => 'required|string',
            '*.kode_mesin'        => 'required|string',
            '*.harga_beli'        => 'required',
            '*.tahun_pembelian'   => 'required|integer|min:1900|max:' . date('Y'),
            '*.spesifikasi_mesin' => 'nullable|string',
            '*.daya_motor'        => 'required',
            '*.lokasi_mesin'      => 'required|string',
            '*.nilai_sisa'        => 'nullable',
            '*.umur_ekonomis'     => 'nullable',
            '*.status'            => 'nullable|in:aktif,tidak aktif',
        ];
    }
}
