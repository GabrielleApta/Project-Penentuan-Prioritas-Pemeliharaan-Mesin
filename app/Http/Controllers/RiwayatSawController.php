<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Models\Prioritas;
use App\Models\PenilaianMesin;
use App\Models\RiwayatSaw;
use Illuminate\Http\Request;

class RiwayatSawController extends Controller
{
   public function index()
{
    $riwayat = RiwayatSaw::select('kode_perhitungan', 'created_at')
                         ->groupBy('kode_perhitungan', 'created_at')
                         ->latest('created_at')
                         ->get();

    return view('pages.riwayat.saw.index', compact('riwayat'));
}

    public function store()
{
    $hasilSAW = Prioritas::with('mesin')->orderBy('rangking')->get();

    if ($hasilSAW->isEmpty()) {
        return back()->with('error', 'Hasil perhitungan SAW belum tersedia.');
    }

    // Generate kode baru setiap kali simpan
    $kode = 'SAW-' . date('Y') . '-' . str_pad(RiwayatSaw::distinct('kode_perhitungan')->count() + 1, 3, '0', STR_PAD_LEFT);

    foreach ($hasilSAW as $item) {
        $penilaian = PenilaianMesin::where('mesin_id', $item->mesin_id)->latest()->first();

        if (!$penilaian) continue;

        RiwayatSaw::create([
            'mesin_id'              => $item->mesin_id,
            'kode_perhitungan'      => $kode,
            'nama_mesin'            => $item->mesin->nama_mesin,
            'akumulasi_penyusutan'  => $penilaian->akumulasi_penyusutan,
            'usia_mesin'            => $penilaian->usia_mesin,
            'frekuensi_kerusakan'   => $penilaian->frekuensi_kerusakan,
            'waktu_downtime'        => $penilaian->waktu_downtime,
            'skor_akhir'            => $item->skor_akhir,
            'ranking'               => $item->rangking,
        ]);
    }

    return redirect()->route('prioritas.index')->with('success', 'Riwayat SAW berhasil disimpan dengan kode: ' . $kode);
}

    public function show($kode)
    {
        $data = RiwayatSaw::where('kode_perhitungan', $kode)->orderBy('ranking')->get();

        if ($data->isEmpty()) {
            abort(404, 'Riwayat tidak ditemukan.');
        }

        return view('pages.riwayat.saw.show', [
            'items' => $data,
            'kode'  => $kode,
            'tahun' => now()->year,
        ]);
    }

    public function destroy($kode)
    {
        RiwayatSaw::where('kode_perhitungan', $kode)->delete();
        return back()->with('success', 'Riwayat berhasil dihapus.');
    }
}
