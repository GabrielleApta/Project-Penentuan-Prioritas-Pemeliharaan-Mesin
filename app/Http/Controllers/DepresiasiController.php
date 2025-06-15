<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Depresiasi;
use App\Exports\DepresiasiExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class DepresiasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $mesins = Mesin::all();

foreach ($mesins as $mesin) {
    $harga_beli = is_numeric($mesin->harga_beli) ? (float) $mesin->harga_beli : 0;
    $nilai_sisa = is_numeric($mesin->nilai_sisa) ? (float) $mesin->nilai_sisa : 0;
    $umur_ekonomis = is_numeric($mesin->umur_ekonomis) ? (int) $mesin->umur_ekonomis : 0;

    $depresiasi_tahunan = ($umur_ekonomis > 0) ? ($harga_beli - $nilai_sisa) / $umur_ekonomis : 0;

    // Tambahkan nilai ke dalam objek mesin
    $mesin->depresiasi_tahunan = $depresiasi_tahunan;

    // lanjutkan logika buat depresiasi per tahun...
    $tahun_sekarang = now()->year;
    $tahun_akhir = max($mesin->tahun_pembelian + $umur_ekonomis - 1, $tahun_sekarang);
    $akumulasi = 0;

    for ($i = 0; $i <= ($tahun_akhir - $mesin->tahun_pembelian); $i++) {
    $tahun = $mesin->tahun_pembelian + $i;

    if ($i < $umur_ekonomis) {
        $penyusutan = $depresiasi_tahunan;
        $akumulasi += $penyusutan;
        $nilai_buku = max($harga_beli - $akumulasi, $nilai_sisa);
    } else {
        $penyusutan = 0;
        $akumulasi = $harga_beli - $nilai_sisa;
        $nilai_buku = $nilai_sisa;
    }

    $existing = Depresiasi::where('mesin_id', $mesin->id)->where('tahun', $tahun)->first();
    if (!$existing) {
        Depresiasi::create([
            'mesin_id' => $mesin->id,
            'tahun' => $tahun,
            'penyusutan' => $penyusutan,
            'akumulasi_penyusutan' => $akumulasi,
            'nilai_buku' => $nilai_buku,
        ]);
    }
}
// ⬇ Tambahan: ambil akumulasi penyusutan tahun terakhir
        $latestDepresiasi = Depresiasi::where('mesin_id', $mesin->id)
            ->orderByDesc('tahun')
            ->first();

        $mesin->total_akumulasi = $latestDepresiasi ? $latestDepresiasi->akumulasi_penyusutan : 0;
        $mesin->nilai_buku_akhir = $latestDepresiasi ? $latestDepresiasi->nilai_buku : 0;

    }

    return view('pages.depresiasi.index', compact('mesins'));
}

    public function grafik(Request $request)
    {
        $mesins = Mesin::all();
        $mesinid = $request->input('mesin_id');

        $query = Depresiasi::select('mesin_id', 'tahun', 'penyusutan')
            ->with('mesin')
            ->orderBy('tahun', 'ASC');

        if (!empty($mesinid)) {
            $query->where('mesin_id', $mesinid);
        }

        $depresiasiData = $query->get();

        if ($depresiasiData->isEmpty()) {
            return view('pages.depresiasi.grafik', compact('mesins', 'mesinid'))
                ->with('error', 'Data depresiasi tidak ditemukan untuk mesin yang dipilih.');
        }

        $data = [];
        $tahun = [];

        foreach ($depresiasiData as $item) {
            if ($item->mesin) {
                $mesinNama = $item->mesin->nama_mesin;
                $harga_beli = (float) $item->mesin->harga_beli;
                $nilai_sisa = (float) $item->mesin->nilai_sisa;
                $umur_ekonomis = (int) $item->mesin->umur_ekonomis;
                $penyusutan = (float) $item->penyusutan;

                if (!isset($data[$mesinNama])) {
                    $data[$mesinNama] = [
                        'label' => $mesinNama,
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                        'data' => []
                    ];
                }

                $nilai_buku = max($harga_beli - ($penyusutan * ($item->tahun - $item->mesin->tahun_pembelian)), $nilai_sisa);

                $data[$mesinNama]['data'][] = $nilai_buku;
                $tahun[] = (int) $item->tahun;
            }
        }

        $tahun = array_values(array_unique($tahun));
        $nilaiBukuData = array_values($data);

        return view('pages.depresiasi.grafik', compact('mesins', 'mesinid', 'tahun', 'nilaiBukuData'));
    }

    public function show($id)
    {
        $mesin = Mesin::findOrFail($id);
        $depresiasi = Depresiasi::where('mesin_id', $id)->orderBy('tahun', 'ASC')->get();

        return view('pages.depresiasi.detail', compact('mesin', 'depresiasi'));
    }

    public function reset()
    {
        Depresiasi::truncate();
        $mesins = Mesin::all();

        foreach ($mesins as $mesin) {
            $harga_beli = is_numeric($mesin->harga_beli) ? (float) $mesin->harga_beli : 0;
            $nilai_sisa = is_numeric($mesin->nilai_sisa) ? (float) $mesin->nilai_sisa : 0;
            $umur_ekonomis = is_numeric($mesin->umur_ekonomis) ? (int) $mesin->umur_ekonomis : 0;

            $depresiasi_tahunan = ($umur_ekonomis > 0) ? ($harga_beli - $nilai_sisa) / $umur_ekonomis : 0;

$tahun_sekarang = now()->year;
$tahun_akhir = max($mesin->tahun_pembelian + $umur_ekonomis - 1, $tahun_sekarang);
$akumulasi = 0;

for ($i = 0; $i <= ($tahun_akhir - $mesin->tahun_pembelian); $i++) {
    $tahun = $mesin->tahun_pembelian + $i;

    if ($i < $umur_ekonomis) {
        $penyusutan = $depresiasi_tahunan;
        $akumulasi += $penyusutan;
        $nilai_buku = max($harga_beli - $akumulasi, $nilai_sisa);
    } else {
        $penyusutan = 0;
        $akumulasi = $harga_beli - $nilai_sisa;
        $nilai_buku = $nilai_sisa;
    }

    Depresiasi::create([
        'mesin_id' => $mesin->id,
        'tahun' => $tahun,
        'penyusutan' => $penyusutan,
        'akumulasi_penyusutan' => $akumulasi,
        'nilai_buku' => $nilai_buku,
    ]);
}

        }

        return redirect()->route('depresiasi.index')->with('success', 'Data depresiasi berhasil dihitung ulang.');
    }

    public function exportExcel()
    {
    return Excel::download(new DepresiasiExport, 'depresiasi.xlsx');
    }

    public function exportPdf()
    {
    $mesins = Mesin::with('depresiasi')->get();

    $pdf = PDF::loadView('pages.depresiasi.pdf', compact('mesins'));
    return $pdf->download('laporan_depresiasi.pdf');
    }
}
