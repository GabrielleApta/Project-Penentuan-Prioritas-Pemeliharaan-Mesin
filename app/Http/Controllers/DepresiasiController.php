<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Depresiasi;
use App\Models\RiwayatStraightLine;
use App\Models\RiwayatPerhitungan;
use App\Exports\DepresiasiExport;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Carbon\Carbon;
use DB;

class DepresiasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        // Ambil semua data mesin
        $mesins = Mesin::all();

        // Cek apakah ada data depresiasi yang sudah digenerate
        $hasDepresiasiData = Depresiasi::exists();

        if ($hasDepresiasiData) {
            // Jika ada data depresiasi, hitung dan tampilkan
            $tahunSekarang = now()->year;

            foreach ($mesins as $mesin) {
                // Hitung depresiasi tahunan
                $harga = (float) $mesin->harga_beli;
                $sisa = (float) $mesin->nilai_sisa;
                $umur = (int) $mesin->umur_ekonomis;
                $depresiasi = ($umur > 0) ? ($harga - $sisa) / $umur : 0;
                $mesin->depresiasi_tahunan = $depresiasi;

                // Ambil data depresiasi untuk tahun sekarang
                $depresiasiTahunIni = $mesin->depresiasi()->where('tahun', $tahunSekarang)->first();
                $mesin->total_akumulasi = $depresiasiTahunIni ? $depresiasiTahunIni->akumulasi_penyusutan : 0;
                $mesin->nilai_buku_akhir = $depresiasiTahunIni ? $depresiasiTahunIni->nilai_buku : 0;
            }
        } else {
            // Jika belum ada data depresiasi, set nilai default ke 0
            foreach ($mesins as $mesin) {
                $mesin->depresiasi_tahunan = 0;
                $mesin->total_akumulasi = 0;
                $mesin->nilai_buku_akhir = 0;
            }
        }

        return view('pages.depresiasi.index', compact('mesins', 'hasDepresiasiData'));
    }

    public function generate()
    {
        // Hapus data depresiasi lama jika ada
        Depresiasi::truncate();

        $kode = 'SL-' . now()->format('YmdHis');
        $userId = auth()->id() ?? 1;

        foreach (Mesin::all() as $mesin) {
            $this->hitungDanSimpanDepresiasi($mesin, $kode, $userId);
        }

        // Hapus session jika ada
        session()->forget('depresiasi_disimpan');

        return redirect()->route('depresiasi.index')->with('success', 'Data depresiasi berhasil dihitung.');
    }

    public function reset()
    {
        // Hapus semua data depresiasi
        Depresiasi::truncate();

        $kode = 'SL-' . now()->format('YmdHis');
        $userId = auth()->id() ?? 1;

        foreach (Mesin::all() as $mesin) {
            $this->hitungDanSimpanDepresiasi($mesin, $kode, $userId);
        }

        // Hapus session jika ada
        session()->forget('depresiasi_disimpan');

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

            Depresiasi::create([
                'mesin_id' => $mesin->id,
                'tahun' => $tahun,
                'kode_perhitungan' => $kode,
                'dibuat_oleh' => $userId,
                'tanggal_generate' => now(),
                'penyusutan' => $penyusutan,
                'akumulasi_penyusutan' => $akumulasi,
                'nilai_buku' => $nilai_buku,
            ]);
        }
    }

    public function simpanKeRiwayat()
    {
        // Cek apakah sudah ada data depresiasi
        if (!Depresiasi::exists()) {
            return redirect()->route('depresiasi.index')->with('error', 'Tidak ada data depresiasi untuk disimpan. Silakan generate data terlebih dahulu.');
        }

        $kode = 'SL-' . now()->format('YmdHis');

        foreach (Mesin::all() as $mesin) {
            // Ambil data depresiasi terakhir untuk mesin ini
            $depresiasiTerakhir = $mesin->depresiasi()->orderBy('tahun', 'desc')->first();

            if ($depresiasiTerakhir) {
                $harga = (float) $mesin->harga_beli;
                $sisa = (float) $mesin->nilai_sisa;
                $umur = (int) $mesin->umur_ekonomis;
                $depresiasi_tahunan = $umur > 0 ? ($harga - $sisa) / $umur : 0;

                RiwayatStraightLine::create([
                    'mesin_id'                => $mesin->id,
                    'kode_perhitungan'       => $kode,
                    'nama_mesin'             => $mesin->nama_mesin,
                    'tahun_pembelian'        => $mesin->tahun_pembelian,
                    'harga_beli'             => $mesin->harga_beli,
                    'nilai_sisa'             => $mesin->nilai_sisa,
                    'umur_ekonomis'          => $mesin->umur_ekonomis,
                    'usia_mesin'             => now()->year - $mesin->tahun_pembelian,
                    'penyusutan_per_tahun'   => $depresiasi_tahunan,
                    'akumulasi_penyusutan'   => $depresiasiTerakhir->akumulasi_penyusutan,
                    'nilai_buku'             => $depresiasiTerakhir->nilai_buku,
                ]);
            }
        }

        return redirect()->route('depresiasi.index')->with('success', 'Data berhasil disimpan ke riwayat.');
    }

    private function generateKode($prefix = 'SL')
    {
        return $prefix . '-' . now()->format('YmdHis') . '-' . rand(100, 999);
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
