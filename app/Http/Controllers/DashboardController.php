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
    $totalMesin = Mesin::count();
    $mesinSamjin = Mesin::where('nama_mesin', 'LIKE', '%Samjin%')->count();
    $mesinTwisting = Mesin::where('nama_mesin', 'LIKE', '%Twisting%')->count();
    $mesinAktif = Mesin::where('status', 'aktif')->count();
    $mesinTidakAktif = Mesin::where('status', 'tidakaktif')->count();

    // ====================== GRAFIK DEPRESIASI PER TAHUN ======================
    $tahunAwal = Mesin::min('tahun_pembelian') ?? date('Y');
    $tahunSekarang = date('Y');
    $tahun = range($tahunAwal, $tahunSekarang);

    $nilaiBukuPerTahun = [];

    foreach ($tahun as $th) {
        $topMesin = Depresiasi::with('mesin')
            ->where('tahun', $th)
            ->orderByDesc('nilai_buku')
            ->take(5)
            ->get();

        $datasets = [];
        foreach ($topMesin as $row) {
            $nama = $row->mesin->nama_mesin ?? 'Tidak Diketahui';
            $warna = $this->generateColor($nama);

            $datasets[] = [
                'label' => $nama,
                'data' => [$row->nilai_buku],
                'backgroundColor' => $warna,
                'borderColor' => $warna,
            ];
        }

        $nilaiBukuPerTahun[$th] = $datasets;
    }

    // ====================== GRAFIK SAW ======================
    $hasilSaw = Prioritas::with('mesin')
        ->orderByDesc('skor_akhir')
        ->take(5)
        ->get();

    $labelsSaw = $hasilSaw->pluck('mesin.nama_mesin')->toArray();
    $dataSaw = $hasilSaw->pluck('skor_akhir')->toArray();

    // ====================== KIRIM KE VIEW ======================
    return view('dashboard.index', compact(
        'totalMesin', 'mesinSamjin', 'mesinTwisting', 'mesinAktif', 'mesinTidakAktif',
        'tahun', 'nilaiBukuPerTahun', 'labelsSaw', 'dataSaw'
    ));
}

    // ====================== GENERATE WARNA DARI NAMA MESIN ======================
    private function generateColor($nama)
    {
        $hash = crc32($nama);
        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = ($hash & 0x0000FF);
        return "rgb($r, $g, $b)";
    }
}
