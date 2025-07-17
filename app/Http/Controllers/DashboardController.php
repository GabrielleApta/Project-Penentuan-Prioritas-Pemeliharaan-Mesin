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
        // ====================== STATISTIK CARD ======================
        $statistik = $this->getStatistikMesin();

        // ====================== GRAFIK DEPRESIASI STRAIGHT LINE ======================
        $grafikBubbleDepresiasi = $this->getGrafikBubbleDepresiasi();

        // ====================== GRAFIK SAW ======================
        $grafikSAW = $this->getGrafikSAW();

        // ====================== KIRIM KE VIEW ======================
        return view('dashboard.index', array_merge(
            $statistik,
            $grafikSAW,
            compact('grafikBubbleDepresiasi')
        ));
    }

    // ====================== FUNGSI: STATISTIK ======================
    private function getStatistikMesin()
    {
        return [
            'totalMesin'       => Mesin::count(),
            'mesinSamjin'      => Mesin::where('nama_mesin', 'LIKE', '%Samjin%')->count(),
            'mesinTwisting'    => Mesin::where('nama_mesin', 'LIKE', '%Twisting%')->count(),
            'mesinAktif'       => Mesin::where('status', 'aktif')->count(),
            'mesinTidakAktif'  => Mesin::where('status', 'tidakaktif')->count(),
            'listTahun'        => range(Mesin::min('tahun_pembelian') ?? date('Y'), date('Y')),
        ];
    }

    // ====================== FUNGSI: GRAFIK BUBBLE STRAIGHT LINE ======================
    private function getGrafikBubbleDepresiasi()
    {
        $grafik = [];
        $tahunList = range(Mesin::min('tahun_pembelian') ?? date('Y'), date('Y'));

        foreach ($tahunList as $tahun) {
            $depresiasiMesin = Depresiasi::with('mesin')
                ->where('tahun', $tahun)
                ->get();

            foreach ($depresiasiMesin as $item) {
                $mesin = $item->mesin;
                if (!$mesin) continue;

                $grafik[] = [
                    'x' => (int) $tahun,
                    'y' => (float) $item->nilai_buku,
                    'r' => round((float) $item->penyusutan_tahunan / 1_000_000, 2), // bubble size
                    'label' => $mesin->nama_mesin,
                    'backgroundColor' => $this->generateColor($mesin->nama_mesin),
                ];
            }
        }

        return $grafik;
    }

    // ====================== FUNGSI: GRAFIK SAW ======================
    private function getGrafikSAW()
    {
        $hasilSaw = Prioritas::with('mesin')
            ->orderByDesc('skor_akhir')
            ->take(5)
            ->get();

        return [
            'labelsSaw' => $hasilSaw->map(function ($row) {
                $nama = $row->mesin->nama_mesin ?? 'Tidak Diketahui';
                $skor = number_format($row->skor_akhir, 3);
                return "{$nama} - {$skor}";
            })->toArray(),
            'dataSaw' => $hasilSaw->pluck('skor_akhir')->toArray(),
        ];
    }

    // ====================== FUNGSI: GENERATE WARNA DARI NAMA ======================
    private function generateColor($nama)
    {
        $hash = crc32($nama);
        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = ($hash & 0x0000FF);
        return "rgb($r, $g, $b)";
    }
}
