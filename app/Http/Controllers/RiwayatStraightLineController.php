<?php

namespace App\Http\Controllers;

use App\Models\RiwayatStraightLine;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatStraightLineController extends Controller
{
    public function index()
    {
        $riwayats = RiwayatStraightLine::select('kode_perhitungan', DB::raw('MAX(created_at) as created_at'))
                        ->groupBy('kode_perhitungan')
                        ->orderByDesc('created_at')
                        ->get();

        return view('pages.riwayat.straight_line.index', compact('riwayats'));
    }

    public function simpan(Request $request)
    {
        $kode = RiwayatStraightLine::generateKode();
        $mesins = Mesin::all();
        $tahun = now()->year;

        foreach ($mesins as $mesin) {
            $penyusutan = ($mesin->harga_beli - $mesin->nilai_sisa) / $mesin->umur_ekonomis;
            $usia = $tahun - $mesin->tahun_pembelian;
            $akumulasi = $penyusutan * min($usia, $mesin->umur_ekonomis);
            $nilaiBuku = max($mesin->harga_beli - $akumulasi, $mesin->nilai_sisa);

            RiwayatStraightLine::create([
                'mesin_id' => $mesin->id,
                'kode_perhitungan' => $kode,
                'nama_mesin' => $mesin->nama_mesin,
                'tahun_pembelian' => $mesin->tahun_pembelian,
                'harga_beli' => $mesin->harga_beli,
                'nilai_sisa' => $mesin->nilai_sisa,
                'umur_ekonomis' => $mesin->umur_ekonomis,
                'usia_mesin' => $usia,
                'penyusutan_per_tahun' => $penyusutan,
                'akumulasi_penyusutan' => $akumulasi,
                'nilai_buku' => $nilaiBuku,
            ]);
        }

        return redirect()->back()->with('success', 'Data disimpan ke riwayat dengan kode ' . $kode);
    }

    public function detail($kode)
    {
        $riwayat = RiwayatStraightLine::where('kode_perhitungan', $kode)->get();
        return view('pages.riwayat.straight_line.detail', compact('riwayat', 'kode'));
    }

    public function destroy($kode)
{
    RiwayatStraightLine::where('kode_perhitungan', $kode)->delete();

    // Redirect ke index, bukan ke destroy
    return redirect()->route('riwayat.straight-line.index')
                     ->with('success', 'Riwayat ' . $kode . ' berhasil dihapus.');
}

}
