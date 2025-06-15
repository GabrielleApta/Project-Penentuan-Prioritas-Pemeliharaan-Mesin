<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\HasilSaw;
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
        $mesins = Mesin::with(['depresiasi', 'kerusakanTahunan'])->get();

        // Tahun patokan
        $tahunSekarang = date('Y');
        $tahunAwal = $tahunSekarang - 2;

        $data_nilai = [];
        foreach ($mesins as $mesin) {
            // Ambil akumulasi penyusutan terbaru
            $depresiasiTerakhir = $mesin->depresiasi->sortByDesc('tahun')->first();
            $akumulasi = $depresiasiTerakhir ? $depresiasiTerakhir->akumulasi_penyusutan : 0;

            $usia = $tahunSekarang - $mesin->tahun_pembelian;
            $frekuensi = $mesin->skorFrekuensiKerusakan($tahunAwal, $tahunSekarang);
            $downtime = $mesin->skorDowntime($tahunAwal, $tahunSekarang);

            $data_nilai[] = [
                'mesin_id' => $mesin->id,
                'akumulasi_penyusutan' => $akumulasi,
                'usia_mesin' => $usia,
                'frekuensi_kerusakan' => $frekuensi,
                'waktu_downtime' => $downtime,
            ];
        }

        // Normalisasi
        $kriteria = ['akumulasi_penyusutan', 'usia_mesin', 'frekuensi_kerusakan', 'waktu_downtime'];
        $jenis = ['cost', 'cost', 'cost', 'cost']; // Semua cost
        $bobot = ['akumulasi_penyusutan' => 0.3, 'usia_mesin' => 0.3, 'frekuensi_kerusakan' => 0.2, 'waktu_downtime' => 0.2];

        $normalisasi = [];
        foreach ($kriteria as $i => $k) {
            $values = array_column($data_nilai, $k);
            $max = max($values);
            $min = min($values);

            foreach ($data_nilai as $j => $item) {
                $val = $item[$k];
                if (!isset($normalisasi[$j])) $normalisasi[$j] = ['mesin_id' => $item['mesin_id']];
                $normalisasi[$j][$k] = $jenis[$i] === 'cost'
                    ? ($val > 0 ? $min / $val : 0)
                    : ($max > 0 ? $val / $max : 0);
            }
        }

        // Hitung skor akhir
        $hasil = [];
        foreach ($normalisasi as $row) {
            $skor = 0;
            foreach ($bobot as $k => $b) {
                $skor += $row[$k] * $b;
            }
            $hasil[] = ['mesin_id' => $row['mesin_id'], 'skor_akhir' => round($skor, 4)];
        }

        usort($hasil, fn($a, $b) => $b['skor_akhir'] <=> $a['skor_akhir']);

        // Simpan ke DB
        DB::table('hasil_saw')->truncate();
        foreach ($hasil as $i => &$row) {
            $row['rangking'] = $i + 1;
        }
        HasilSaw::insert($hasil);

        return redirect()->route('prioritas.index')->with('success', 'Perhitungan SAW berhasil dilakukan.');
    }

    public function detailSAW($mesin_id)
    {
        $mesin = Mesin::with(['depresiasi', 'kerusakanTahunan'])->find($mesin_id);
        if (!$mesin) return redirect()->route('prioritas.index')->with('error', 'Mesin tidak ditemukan.');

        $tahunSekarang = date('Y');
        $tahunAwal = $tahunSekarang - 2;

        $depresiasiTerakhir = $mesin->depresiasi->sortByDesc('tahun')->first();
        $akumulasi = $depresiasiTerakhir ? $depresiasiTerakhir->akumulasi_penyusutan : 0;

        $usia = $tahunSekarang - $mesin->tahun_pembelian;
        $frekuensi = $mesin->skorFrekuensiKerusakan($tahunAwal, $tahunSekarang);
        $downtime = $mesin->skorDowntime($tahunAwal, $tahunSekarang);

        $data = [
            'akumulasi_penyusutan' => $akumulasi,
            'usia_mesin' => $usia,
            'frekuensi_kerusakan' => $frekuensi,
            'waktu_downtime' => $downtime,
        ];

        // Normalisasi ulang manual untuk satu mesin
        $semua = Mesin::with(['depresiasi', 'kerusakanTahunan'])->get();
        $all_data = [];
        foreach ($semua as $m) {
            $depresiasi = $m->depresiasi->sortByDesc('tahun')->first();
            $all_data[] = [
                'akumulasi_penyusutan' => $depresiasi ? $depresiasi->akumulasi_penyusutan : 0,
                'usia_mesin' => $tahunSekarang - $m->tahun_pembelian,
                'frekuensi_kerusakan' => $m->skorFrekuensiKerusakan($tahunAwal, $tahunSekarang),
                'waktu_downtime' => $m->skorDowntime($tahunAwal, $tahunSekarang),
            ];
        }

        $normalisasi = [];
        $bobot = ['akumulasi_penyusutan' => 0.3, 'usia_mesin' => 0.3, 'frekuensi_kerusakan' => 0.2, 'waktu_downtime' => 0.2];

        foreach ($data as $key => $val) {
            $values = array_column($all_data, $key);
            $max = max($values);
            $min = min($values);

            $normalisasi[$key] = $val > 0 ? $min / $val : 0;
        }

        $skor_akhir = 0;
        foreach ($bobot as $k => $b) {
            $skor_akhir += ($normalisasi[$k] ?? 0) * $b;
        }

        return view('pages.prioritas.detail', compact('mesin', 'data', 'normalisasi', 'skor_akhir'));
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
        $mesin = Mesin::with(['depresiasi', 'kerusakanTahunan'])->find($mesin_id);
        if (!$mesin) return redirect()->route('prioritas.index')->with('error', 'Mesin tidak ditemukan.');

        $tahunSekarang = date('Y');
        $tahunAwal = $tahunSekarang - 2;

        $depresiasi = $mesin->depresiasi->sortByDesc('tahun')->first();
        $data = [
            'akumulasi_penyusutan' => $depresiasi ? $depresiasi->akumulasi_penyusutan : 0,
            'usia_mesin' => $tahunSekarang - $mesin->tahun_pembelian,
            'frekuensi_kerusakan' => $mesin->skorFrekuensiKerusakan($tahunAwal, $tahunSekarang),
            'waktu_downtime' => $mesin->skorDowntime($tahunAwal, $tahunSekarang),
        ];

        $semua = Mesin::with(['depresiasi', 'kerusakanTahunan'])->get();
        $all_data = [];
        foreach ($semua as $m) {
            $dep = $m->depresiasi->sortByDesc('tahun')->first();
            $all_data[] = [
                'akumulasi_penyusutan' => $dep ? $dep->akumulasi_penyusutan : 0,
                'usia_mesin' => $tahunSekarang - $m->tahun_pembelian,
                'frekuensi_kerusakan' => $m->skorFrekuensiKerusakan($tahunAwal, $tahunSekarang),
                'waktu_downtime' => $m->skorDowntime($tahunAwal, $tahunSekarang),
            ];
        }

        $normalisasi = [];
        $bobot = ['akumulasi_penyusutan' => 0.3, 'usia_mesin' => 0.3, 'frekuensi_kerusakan' => 0.2, 'waktu_downtime' => 0.2];

        foreach ($data as $key => $val) {
            $values = array_column($all_data, $key);
            $min = min($values);
            $normalisasi[$key] = $val > 0 ? $min / $val : 0;
        }

        $skor_akhir = 0;
        foreach ($bobot as $k => $b) {
            $skor_akhir += ($normalisasi[$k] ?? 0) * $b;
        }

        if ($isPDF) {
            $pdf = PDF::loadView('pages.prioritas.detailPDF', compact('mesin', 'data', 'normalisasi', 'skor_akhir'));
            return $pdf->stream("detail_saw_{$mesin_id}.pdf");
        }

        return view('pages.prioritas.detail', compact('mesin', 'data', 'normalisasi', 'skor_akhir'));
    }
}
