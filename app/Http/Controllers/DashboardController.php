<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Depresiasi;
use App\Models\HasilSaw;

class DashboardController extends Controller
{
    public function index()
    {
        // ====================== TOTAL MESIN ======================
        $totalMesin = Mesin::count();
        $mesinSamjin = Mesin::where('nama_mesin', 'LIKE', '%Samjin%')->count();
        $mesinTwisting = Mesin::where('nama_mesin', 'LIKE', '%Twisting%')->count();
        $mesinAktif = Mesin::where('status', 'aktif')->count();
        $mesinTidakAktif = Mesin::where('status', 'tidakaktif')->count();

        // ====================== GRAFIK DEPRESIASI ======================
        $tahunAwal = Mesin::min('tahun_pembelian') ?? date('Y');
        $tahunSekarang = date('Y');
        $tahun = range($tahunAwal, $tahunSekarang);

        $depresiasiData = Depresiasi::with('mesin')
            ->whereIn('tahun', $tahun)
            ->orderBy('tahun', 'ASC')
            ->get()
            ->groupBy(fn($d) => $d->mesin->nama_mesin ?? 'Tidak Diketahui');

        $nilaiBuku = [];

        foreach ($depresiasiData as $namaMesin => $data) {
            $dataPerTahun = collect($tahun)->map(function ($th) use ($data) {
                return optional($data->firstWhere('tahun', $th))->nilai_buku ?? null;
            })->toArray();

            $warna = $this->generateColor($namaMesin);

            $nilaiBuku[] = [
                'label' => $namaMesin,
                'data' => $dataPerTahun,
                'borderColor' => $warna,
                'backgroundColor' => $warna,
                'fill' => false,
            ];
        }

        // ====================== GRAFIK SAW ======================
        $hasilSaw = HasilSaw::with('mesin')
            ->orderByDesc('skor_akhir')
            ->take(5)
            ->get();

        $labelsSaw = $hasilSaw->pluck('mesin.nama_mesin')->toArray();
        $dataSaw = $hasilSaw->pluck('skor_akhir')->toArray();

        // ====================== KIRIM KE VIEW ======================
        return view('dashboard.index', compact(
            'totalMesin', 'mesinSamjin', 'mesinTwisting', 'mesinAktif', 'mesinTidakAktif',
            'tahun', 'nilaiBuku', 'labelsSaw', 'dataSaw'
        ));
    }

    // ====================== WARNA KONSISTEN DARI NAMA MESIN ======================
    private function generateColor($nama)
    {
        $hash = crc32($nama);
        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = ($hash & 0x0000FF);
        return "rgb($r, $g, $b)";
    }
}
