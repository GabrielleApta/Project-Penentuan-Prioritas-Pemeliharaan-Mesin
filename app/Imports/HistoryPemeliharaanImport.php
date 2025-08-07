<?php

namespace App\Imports;

use App\Models\HistoryPemeliharaan;
use App\Models\Mesin;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Carbon\Carbon;

class HistoryPemeliharaanImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        // Skip jika row kosong atau nama mesin tidak ada
        if (empty($row['nama_mesin'])) {
            return null;
        }

        // Cari mesin berdasarkan nama
        $mesin = Mesin::where('nama_mesin', trim($row['nama_mesin']))->first();

        if (!$mesin) {
            return null; // Skip jika mesin tidak ditemukan
        }

        // Handle tanggal
        $tanggal = $row['tanggal'];
        if (is_numeric($tanggal)) {
            // Jika tanggal dalam format Excel serial number
            $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
        } else {
            // Jika tanggal dalam format text
            try {
                $tanggal = Carbon::createFromFormat('Y-m-d', $tanggal)->format('Y-m-d');
            } catch (\Exception $e) {
                return null; // Skip jika format tanggal salah
            }
        }

        return new HistoryPemeliharaan([
            'mesin_id' => $mesin->id,
            'tanggal' => $tanggal,
            'jenis_pemeliharaan' => strtolower(trim($row['jenis_pemeliharaan'] ?? 'preventive')),
            'deskripsi' => $row['deskripsi'] ?? '',
            'durasi_jam' => (float) ($row['durasi_jam'] ?? 0),
            'teknisi' => $row['teknisi'] ?? '',
            'verifikasi' => (int) ($row['verifikasi'] ?? 0),
        ]);
    }
}
