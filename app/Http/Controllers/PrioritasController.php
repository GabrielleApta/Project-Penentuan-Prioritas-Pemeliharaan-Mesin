<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\HasilSaw;
use App\Models\PenilaianMesin;
use Illuminate\Support\Facades\DB;
use PDF;

class PrioritasController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $hasil_saw = HasilSaw::with('mesin')->orderBy('rangking')->get();
        return view('pages.prioritas.index', compact('hasil_saw'));
    }

    public function hitungSAW()
    {
        $tahunPenilaian = 2024;

        $data_penilaian = PenilaianMesin::with('mesin')
            ->where('tahun_penilaian', $tahunPenilaian)
            ->get();

        if ($data_penilaian->isEmpty()) {
            return redirect()->back()->with('error', 'Data penilaian belum tersedia untuk tahun ' . $tahunPenilaian);
        }

        // Ambil data mentah
        $data_nilai = $data_penilaian->map(function ($item) {
            return [
                'mesin_id' => $item->mesin_id,
                'akumulasi_penyusutan' => $item->akumulasi_penyusutan,
                'usia_mesin' => $item->usia_mesin,
                'frekuensi_kerusakan' => $item->frekuensi_kerusakan,
                'waktu_downtime' => $item->waktu_downtime,
            ];
        })->toArray();

        // Kriteria & Bobot SAW
        $kriteria = ['akumulasi_penyusutan', 'usia_mesin', 'frekuensi_kerusakan', 'waktu_downtime'];
        $bobot = [
            'akumulasi_penyusutan' => 0.3,
            'usia_mesin' => 0.3,
            'frekuensi_kerusakan' => 0.2,
            'waktu_downtime' => 0.2,
        ];

        // Normalisasi (semua kriteria bertipe cost → min / value)
        $normalisasi = [];
        foreach ($kriteria as $k) {
            $values = array_column($data_nilai, $k);
            $min = min($values) ?: 0.0001; // Hindari divide by zero

            foreach ($data_nilai as $j => $item) {
                if (!isset($normalisasi[$j])) {
                    $normalisasi[$j] = ['mesin_id' => $item['mesin_id']];
                }

                $val = $item[$k];
                $normalisasi[$j][$k] = $val > 0 ? $min / $val : 0;
            }
        }

        // Hitung skor akhir
        $hasil = [];
        foreach ($normalisasi as $row) {
            $skor = 0;
            foreach ($bobot as $k => $b) {
                $skor += $row[$k] * $b;
            }

            $hasil[] = [
                'mesin_id' => $row['mesin_id'],
                'skor_akhir' => round($skor, 4),
            ];
        }

        // Urutkan dan beri ranking
        usort($hasil, fn($a, $b) => $b['skor_akhir'] <=> $a['skor_akhir']);

        DB::table('hasil_saw')->truncate();
        foreach ($hasil as $i => &$row) {
            $row['rangking'] = $i + 1;
        }

        HasilSaw::insert($hasil);

        return redirect()->route('prioritas.index')->with('success', 'Perhitungan SAW berhasil dilakukan.');
    }

    public function detailSAW($mesin_id)
    {
        $tahunPenilaian = 2024;

        $penilaian = PenilaianMesin::where('mesin_id', $mesin_id)
            ->where('tahun_penilaian', $tahunPenilaian)
            ->firstOrFail();

        $data = [
            'akumulasi_penyusutan' => $penilaian->akumulasi_penyusutan,
            'usia_mesin' => $penilaian->usia_mesin,
            'frekuensi_kerusakan' => $penilaian->frekuensi_kerusakan,
            'waktu_downtime' => $penilaian->waktu_downtime,
        ];

        $semua = PenilaianMesin::where('tahun_penilaian', $tahunPenilaian)->get();

        $normalisasi = [];
        $bobot = [
            'akumulasi_penyusutan' => 0.3,
            'usia_mesin' => 0.3,
            'frekuensi_kerusakan' => 0.2,
            'waktu_downtime' => 0.2,
        ];

        foreach ($data as $key => $val) {
            $values = $semua->pluck($key)->toArray();
            $min = min($values) ?: 0.0001;

            $normalisasi[$key] = $val > 0 ? $min / $val : 0;
        }

        $skor_akhir = 0;
        foreach ($bobot as $k => $b) {
            $skor_akhir += ($normalisasi[$k] ?? 0) * $b;
        }

        return view('pages.prioritas.detail', [
            'mesin' => $penilaian->mesin,
            'data' => $data,
            'normalisasi' => $normalisasi,
            'skor_akhir' => $skor_akhir,
        ]);
    }

    public function printPDF()
    {
        $hasil_saw = HasilSaw::with('mesin')->orderBy('rangking')->get();
        $pdf = PDF::loadView('pages.prioritas.printPDF', compact('hasil_saw'));
        return $pdf->stream('hasil_saw.pdf');
    }

    public function detailPDF($mesin_id)
    {
        return $this->cetakDetail($mesin_id, true);
    }

    private function cetakDetail($mesin_id, $isPDF = false)
    {
        $tahunPenilaian = 2024;

        $penilaian = PenilaianMesin::with('mesin')
            ->where('mesin_id', $mesin_id)
            ->where('tahun_penilaian', $tahunPenilaian)
            ->firstOrFail();

        $data = [
            'akumulasi_penyusutan' => $penilaian->akumulasi_penyusutan,
            'usia_mesin' => $penilaian->usia_mesin,
            'frekuensi_kerusakan' => $penilaian->frekuensi_kerusakan,
            'waktu_downtime' => $penilaian->waktu_downtime,
        ];

        $semua = PenilaianMesin::where('tahun_penilaian', $tahunPenilaian)->get();

        $normalisasi = [];
        $bobot = [
            'akumulasi_penyusutan' => 0.3,
            'usia_mesin' => 0.3,
            'frekuensi_kerusakan' => 0.2,
            'waktu_downtime' => 0.2,
        ];

        foreach ($data as $key => $val) {
            $values = $semua->pluck($key)->toArray();
            $min = min($values) ?: 0.0001;

            $normalisasi[$key] = $val > 0 ? $min / $val : 0;
        }

        $skor_akhir = 0;
        foreach ($bobot as $k => $b) {
            $skor_akhir += ($normalisasi[$k] ?? 0) * $b;
        }

        if ($isPDF) {
            $pdf = PDF::loadView('pages.prioritas.detailPDF', [
                'mesin' => $penilaian->mesin,
                'data' => $data,
                'normalisasi' => $normalisasi,
                'skor_akhir' => $skor_akhir,
            ]);
            return $pdf->stream("detail_saw_{$mesin_id}.pdf");
        }

        return view('pages.prioritas.detail', [
            'mesin' => $penilaian->mesin,
            'data' => $data,
            'normalisasi' => $normalisasi,
            'skor_akhir' => $skor_akhir,
        ]);
    }
}
