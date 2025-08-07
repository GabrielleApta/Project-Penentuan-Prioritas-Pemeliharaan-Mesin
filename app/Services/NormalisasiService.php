<?php
// File: app/Services/NormalisasiService.php

namespace App\Services;

class NormalisasiService
{
    /**
     * Konfigurasi kriteria dan bobot
     */
    public static $kriteria = [
        'akumulasi_penyusutan' => ['bobot' => 0.3, 'jenis' => 'cost'],
        'usia_mesin'           => ['bobot' => 0.3, 'jenis' => 'cost'],
        'frekuensi_kerusakan'  => ['bobot' => 0.2, 'jenis' => 'cost'],
        'waktu_downtime'       => ['bobot' => 0.2, 'jenis' => 'cost'],
    ];

    /**
     * Hitung normalisasi untuk semua kriteria
     */
    public static function hitungNormalisasi(array $data)
    {
        if (empty($data)) {
            return [];
        }

        // Debug: Log input data
        \Log::info('Input data for normalisasi:', $data);

        // Cari nilai minimum untuk setiap kriteria (semua cost criteria)
        $minValues = [];
        foreach (self::$kriteria as $kriteriaName => $info) {
            $values = array_column($data, $kriteriaName);
            $validValues = array_filter($values, fn($v) => $v > 0);
            $minValues[$kriteriaName] = !empty($validValues) ? min($validValues) : 0.0001;

            // Debug: Log min values
            \Log::info("Min value for {$kriteriaName}: {$minValues[$kriteriaName]}");
        }

        $result = [];

        foreach ($data as $index => $item) {
            $normalisasi = ['mesin_id' => $item['mesin_id']];

            foreach (self::$kriteria as $kriteriaName => $info) {
                $currentValue = $item[$kriteriaName];
                $minValue = $minValues[$kriteriaName];

                if ($info['jenis'] === 'cost') {
                    // Cost criteria: min/current (semakin kecil semakin baik)
                    $normalized = $currentValue > 0 ? $minValue / $currentValue : 1;
                    $normalisasi[$kriteriaName] = $normalized;

                    // Debug: Log normalization process
                    \Log::info("Mesin {$item['mesin_id']} - {$kriteriaName}: {$currentValue} -> {$normalized}");
                } else {
                    // Benefit criteria: current/max (untuk future use)
                    $maxValue = max(array_column($data, $kriteriaName));
                    $normalisasi[$kriteriaName] = $maxValue > 0 ? $currentValue / $maxValue : 0;
                }
            }

            // Preserve original array index AND ensure mesin_id mapping is correct
            $result[$index] = $normalisasi;
        }

        // Debug: Log final result
        \Log::info('Final normalization result:', $result);

        return $result;
    }

    /**
     * Hitung skor akhir SAW dari data normalisasi
     */
    public static function hitungSkorAkhir(array $normalisasiData)
    {
        $hasil = [];

        foreach ($normalisasiData as $data) {
            $skor = 0;
            foreach (self::$kriteria as $kriteria => $info) {
                $skor += ($data[$kriteria] ?? 0) * $info['bobot'];
            }

            $hasil[] = [
                'mesin_id' => $data['mesin_id'],
                'skor_akhir' => round($skor, 4),
            ];
        }

        return $hasil;
    }

    /**
     * Hitung normalisasi untuk view (format display)
     */
    public static function hitungNormalisasiForView($data)
    {
        if (empty($data)) {
            return [];
        }

        // Convert collection to array if needed
        $dataArray = $data instanceof \Illuminate\Support\Collection ? $data->toArray() : $data;

        // Extract data untuk normalisasi
        $inputData = [];
        foreach ($dataArray as $item) {
            $inputData[] = [
                'mesin_id' => $item->mesin_id ?? $item['mesin_id'],
                'akumulasi_penyusutan' => $item->akumulasi_penyusutan ?? $item['akumulasi_penyusutan'],
                'usia_mesin' => $item->usia_mesin ?? $item['usia_mesin'],
                'frekuensi_kerusakan' => $item->frekuensi_kerusakan ?? $item['frekuensi_kerusakan'],
                'waktu_downtime' => $item->waktu_downtime ?? $item['waktu_downtime'],
            ];
        }

        $normalisasiData = self::hitungNormalisasi($inputData);

        $result = [];
        foreach ($normalisasiData as $index => $norm) {
            $originalItem = $dataArray[$index];

            // Hitung skor akhir
            $skor = 0;
            foreach (self::$kriteria as $kriteria => $info) {
                $skor += $norm[$kriteria] * $info['bobot'];
            }

            $result[] = [
                'mesin' => $originalItem->nama_mesin ?? $originalItem['nama_mesin'],
                'norm_penyusutan' => number_format($norm['akumulasi_penyusutan'], 6),
                'norm_usia' => number_format($norm['usia_mesin'], 6),
                'norm_frekuensi' => number_format($norm['frekuensi_kerusakan'], 6),
                'norm_downtime' => number_format($norm['waktu_downtime'], 6),
                'skor_akhir' => number_format($skor, 6),
            ];
        }

        return $result;
    }

    /**
     * Get bobot kriteria
     */
    public static function getBobotKriteria()
    {
        return array_map(fn($info) => $info['bobot'], self::$kriteria);
    }

    /**
     * Get nama kriteria
     */
    public static function getNamaKriteria()
    {
        return array_keys(self::$kriteria);
    }
}
