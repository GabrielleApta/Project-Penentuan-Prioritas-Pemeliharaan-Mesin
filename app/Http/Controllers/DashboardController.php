<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Depresiasi;
use App\Models\KerusakanTahunan;
use App\Models\Prioritas;

class DashboardController extends Controller
{
    public function index()
    {
        $statistik = $this->getStatistikMesin();
        $grafikLineDepresiasi = $this->getGrafikLineDepresiasi(); // default all mesin
        $grafikSAW = $this->getGrafikSAW();

        return view('dashboard.index', array_merge(
            $statistik,
            $grafikSAW,
            compact('grafikLineDepresiasi')
        ));
    }

    // ====================== STATISTIK ======================
    private function getStatistikMesin()
{
    $tahunTarget = 2024;

    // Mesin dengan kerusakan tertinggi (parah)
    $mesinTerbanyak = KerusakanTahunan::where('tahun', $tahunTarget)
        ->orderByDesc('kerusakan_parah')
        ->with('mesin')
        ->first();

    return [
        'totalMesin'      => Mesin::count(),
        'mesinSamjin'     => Mesin::where('nama_mesin', 'LIKE', '%Samjin%')->count(),
        'mesinTwisting'   => Mesin::where('nama_mesin', 'LIKE', '%Twisting%')->count(),
        'mesinAktif'      => Mesin::where('status', 'aktif')->count(),
        'mesinTidakAktif' => Mesin::where('status', 'tidakaktif')->count(),
        'frekuensiRingan' => KerusakanTahunan::where('tahun', $tahunTarget)->sum('kerusakan_ringan'),
        'frekuensiParah'  => KerusakanTahunan::where('tahun', $tahunTarget)->sum('kerusakan_parah'),
        'totalPemeliharaan' => KerusakanTahunan::where('tahun', $tahunTarget)->count(),
        'mesinTerbanyakRusak' => $mesinTerbanyak?->mesin?->nama_mesin ?? 'Data Tidak Ada',
        'jumlahKerusakanTerbanyak' => $mesinTerbanyak?->kerusakan_parah ?? 0,
        'listTahun'       => range(Mesin::min('tahun_pembelian') ?? $tahunTarget, $tahunTarget),
    ];
}


    // ====================== GRAFIK PENYUSUTAN STRAIGHT LINE ======================
    private function getGrafikLineDepresiasi()
{
    $tahunAwal = Mesin::min('tahun_pembelian');
    $tahunAkhir = date('Y');
    $tahunList = $tahunAwal ? range($tahunAwal, $tahunAkhir) : [$tahunAkhir];

    // Ambil semua depresiasi sekaligus
    $depresiasiAll = Depresiasi::whereBetween('tahun', [$tahunAwal, $tahunAkhir])->get()
        ->groupBy('mesin_id');

    // Ambil semua mesin
    $mesinList = Mesin::orderBy('nama_mesin')->get();

    $grafik = [];

    foreach ($mesinList as $mesin) {
        $data = [];
        $depresiasiMesin = $depresiasiAll[$mesin->id] ?? collect();

        foreach ($tahunList as $tahun) {
            $nilai = $depresiasiMesin->firstWhere('tahun', $tahun)->nilai_buku ?? null;
            $data[] = $nilai !== null ? (float) $nilai : null;
        }

        $grafik[] = [
            'label' => $mesin->nama_mesin,
            'data' => $data,
            'borderColor' => $this->generateColor($mesin->nama_mesin),
            'fill' => false,
            'tension' => 0.1
        ];
    }

    return [
        'labelsTahun' => $tahunList,
        'datasets' => $grafik
    ];
}


    // ====================== GRAFIK SAW ======================
    private function getGrafikSAW($limit = 5)
{
    $hasilSaw = Prioritas::with('mesin')
        ->orderBy('rangking') // lebih tepat urut berdasarkan ranking
        ->take($limit)
        ->get();

    $labels = [];
    $data = [];

    foreach ($hasilSaw as $item) {
        $labels[] = $item->mesin->nama_mesin ?? 'Tidak Diketahui';
        $data[] = round($item->skor_akhir, 3);
    }

    return [
        'labelsSaw' => $labels,
        'dataSaw' => $data
    ];
}


    // ====================== AJAX: FILTER MESIN TERTINGGI PENYUSUTANNYA ======================
    public function ajaxTopDepresiasi(Request $request)
    {
        $request->validate([
            'tahun_awal' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $tahunAwal = $request->tahun_awal;
        $tahunAkhir = date('Y');

        // Ambil seluruh data depresiasi dari tahun awal ke sekarang
        $depresiasiData = Depresiasi::whereBetween('tahun', [$tahunAwal, $tahunAkhir])
            ->get()
            ->groupBy('mesin_id');

        $topMesin = [];

        foreach ($depresiasiData as $mesinId => $records) {
            $akumulasi = $records->sum('penyusutan');
            $topMesin[] = [
                'mesin_id' => $mesinId,
                'akumulasi' => $akumulasi
            ];
        }

        // Ambil 5 mesin teratas
        $topMesin = collect($topMesin)
            ->sortByDesc('akumulasi')
            ->take(5)
            ->pluck('mesin_id')
            ->toArray();

        $tahunList = range($tahunAwal, $tahunAkhir);
        $mesinList = Mesin::whereIn('id', $topMesin)->get()->sortBy(function ($mesin) use ($topMesin) {
    return array_search($mesin->id, $topMesin);
})->values();


        $datasets = [];

        foreach ($mesinList as $mesin) {
            $data = [];

            foreach ($tahunList as $tahun) {
                $nilai = Depresiasi::where('mesin_id', $mesin->id)
                    ->where('tahun', $tahun)
                    ->value('nilai_buku');
                $data[] = $nilai !== null ? (float) $nilai : null;
            }

            $datasets[] = [
                'label' => $mesin->nama_mesin,
                'data' => $data,
                'borderColor' => $this->generateColor($mesin->nama_mesin),
                'fill' => false,
                'tension' => 0.1
            ];
        }

        return response()->json([
            'labelsTahun' => $tahunList,
            'datasets' => $datasets
        ]);
    }

    // ====================== UTIL: GENERATE WARNA ======================
    private function generateColor($nama)
    {
        $hash = crc32($nama);
        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = ($hash & 0x0000FF);
        return "rgb($r, $g, $b)";
    }
}
