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

    public function index(Request $request)
{
    $mesins = Mesin::all();

    $tahunSekarang = now()->year;

    foreach ($mesins as $mesin) {
        // Hitung depresiasi tahunan
        $harga = (float) $mesin->harga_beli;
        $sisa = (float) $mesin->nilai_sisa;
        $umur = (int) $mesin->umur_ekonomis;
        $depresiasi = ($umur > 0) ? ($harga - $sisa) / $umur : 0;
        $mesin->depresiasi_tahunan = $depresiasi;

        // Ambil depresiasi tahun sekarang
        $depresiasiTahunIni = $mesin->depresiasi()->where('tahun', $tahunSekarang)->first();
        $mesin->total_akumulasi = $depresiasiTahunIni->akumulasi_penyusutan ?? 0;
        $mesin->nilai_buku_akhir = $depresiasiTahunIni->nilai_buku ?? 0;
    }
    return view('pages.depresiasi.index', compact('mesins'));
}

    public function generate()
    {
        $kode = 'SL-' . now()->format('YmdHis');
        $userId = auth()->id() ?? 1;

        foreach (Mesin::all() as $mesin) {
            $this->hitungDanSimpanDepresiasi($mesin, $kode, $userId);
        }

        return redirect()->route('depresiasi.index')->with('success', 'Data depresiasi berhasil dihitung.');
    }

    public function reset()
    {
        Depresiasi::truncate();

        $kode = 'SL-' . now()->format('YmdHis');
        $userId = auth()->id() ?? 1;

        foreach (Mesin::all() as $mesin) {
            $this->hitungDanSimpanDepresiasi($mesin, $kode, $userId);
        }

        return redirect()->route('depresiasi.index')->with('success', 'Data depresiasi berhasil di-reset dan dihitung ulang.');
    }

    private function hitungDanSimpanDepresiasi($mesin, $kode, $userId)
    {
        $harga = (float) $mesin->harga_beli;
        $sisa = (float) $mesin->nilai_sisa;
        $umur = (int) $mesin->umur_ekonomis;

        $depresiasi = $umur > 0 ? ($harga - $sisa) / $umur : 0;
        $tahun_awal = $mesin->tahun_pembelian;
        $tahun_akhir = max($tahun_awal + $umur - 1, now()->year);

        $akumulasi = 0;

        for ($i = 0; $i <= ($tahun_akhir - $tahun_awal); $i++) {
            $tahun = $tahun_awal + $i;

            if ($i < $umur) {
                $penyusutan = $depresiasi;
                $akumulasi += $penyusutan;
                $nilai_buku = max($harga - $akumulasi, $sisa);
            } else {
                $penyusutan = 0;
                $akumulasi = $harga - $sisa;
                $nilai_buku = $sisa;
            }

            Depresiasi::updateOrCreate(
                ['mesin_id' => $mesin->id, 'tahun' => $tahun],
                [
                    'kode_perhitungan' => $kode,
                    'dibuat_oleh' => $userId,
                    'tanggal_generate' => now(),
                    'penyusutan' => $penyusutan,
                    'akumulasi_penyusutan' => $akumulasi,
                    'nilai_buku' => $nilai_buku,
                ]
            );
        }
    }

    public function show($id)
    {
        $mesin = Mesin::findOrFail($id);
        $depresiasi = Depresiasi::where('mesin_id', $id)->orderBy('tahun', 'ASC')->get();

        return view('pages.depresiasi.detail', compact('mesin', 'depresiasi'));
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

    public function exportExcel()
    {
        return Excel::download(new DepresiasiExport, 'depresiasi.xlsx');
    }

    public function exportPdf()
    {
        $mesins = Mesin::with('depresiasi')->get();

        foreach ($mesins as $mesin) {
            $harga_beli = (float) $mesin->harga_beli;
            $nilai_sisa = (float) $mesin->nilai_sisa;
            $umur_ekonomis = (int) $mesin->umur_ekonomis;
            $mesin->depresiasi_tahunan = ($umur_ekonomis > 0) ? ($harga_beli - $nilai_sisa) / $umur_ekonomis : 0;
        }

        $pdf = PDF::loadView('pages.depresiasi.pdf', compact('mesins'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('Data_Penyusutan_Mesin_' . date('d-m-Y') . '.pdf');
    }
}
