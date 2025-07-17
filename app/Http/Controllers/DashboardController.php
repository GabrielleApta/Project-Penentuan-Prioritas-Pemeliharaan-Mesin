<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Depresiasi;
use App\Models\Prioritas;

class DashboardController extends Controller
{
    public function index()
    {
        // ====================== CARD STATISTIK ======================
        $totalMesin         = Mesin::count();
        $mesinSamjin        = Mesin::where('nama_mesin', 'LIKE', '%Samjin%')->count();
        $mesinTwisting      = Mesin::where('nama_mesin', 'LIKE', '%Twisting%')->count();
        $mesinAktif         = Mesin::where('status', 'aktif')->count();
        $mesinTidakAktif    = Mesin::where('status', 'tidakaktif')->count();

        // ====================== GRAFIK DEPRESIASI PER TAHUN ======================
        $tahunAwal      = Mesin::min('tahun_pembelian') ?? date('Y');
        $tahunSekarang  = date('Y');
        $listTahun      = range($tahunAwal, $tahunSekarang);

        $grafikDepresiasi = [];

        foreach ($listTahun as $tahun) {
            $topMesinTahunIni = Depresiasi::with('mesin')
                ->where('tahun', $tahun)
                ->orderByDesc('nilai_buku')
                ->take(5)
                ->get();

            $datasets = [];
            foreach ($topMesinTahunIni as $row) {
                $nama  = $row->mesin->nama_mesin ?? 'Tidak Diketahui';
                $warna = $this->generateColor($nama);

                $datasets[] = [
                    'label'           => $nama,
                    'data'            => [$row->nilai_buku],
                    'backgroundColor' => $warna,
                    'borderColor'     => $warna,
                ];
            }

            $grafikDepresiasi[$tahun] = $datasets;
        }

        // ====================== GRAFIK SAW ======================
        $hasilSaw = Prioritas::with('mesin')
            ->orderByDesc('skor_akhir')
            ->take(5)
            ->get();

        $labelsSaw = $hasilSaw->map(function ($row) {
            $nama = $row->mesin->nama_mesin ?? 'Tidak Diketahui';
            $skor = number_format($row->skor_akhir, 3);
            return "{$nama} - {$skor}";
        })->toArray();

        $dataSaw = $hasilSaw->pluck('skor_akhir')->toArray();

        // ====================== KIRIM KE VIEW ======================
        return view('dashboard.index', compact(
            'totalMesin',
            'mesinSamjin',
            'mesinTwisting',
            'mesinAktif',
            'mesinTidakAktif',
            'listTahun',
            'grafikDepresiasi',
            'labelsSaw',
            'dataSaw'
        ));
    }

    // ====================== GENERATE WARNA RGB DARI NAMA MESIN ======================
    private function generateColor($nama)
    {
        $hash = crc32($nama);
        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = ($hash & 0x0000FF);
        return "rgb($r, $g, $b)";
    }
}
