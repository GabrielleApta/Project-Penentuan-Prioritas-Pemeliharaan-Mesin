<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatStraightLine;

class RiwayatStraightLineController extends Controller
{
    public function index()
    {
        $riwayat = RiwayatStraightLine::select('kode_perhitungan', 'tanggal_generate', 'dibuat_oleh')
            ->groupBy('kode_perhitungan', 'tanggal_generate', 'dibuat_oleh')
            ->orderBy('tanggal_generate', 'desc')
            ->get();

        return view('pages.riwayat_straight_line.index', compact('riwayat'));
    }

    public function show($kode)
    {
        $riwayat = RiwayatStraightLine::with('mesin', 'user')
            ->where('kode_perhitungan', $kode)
            ->get();

        if ($riwayat->isEmpty())    {
            return redirect()->back()->with('error', 'Data riwayat tidak ditemukan.');
        }

        $tanggalGenerate = $riwayat->first()->tanggal_generate;
        $dibuatOleh = $riwayat->first()->user->name ?? 'Unknown';

        return view('pages.riwayat_straight_line.show', compact('riwayat', 'kode', 'tanggalGenerate', 'dibuatOleh'));
    }

    public function destroy($kode)
{
    $deletedRows = RiwayatStraightLine::where('kode_perhitungan', $kode)->delete();

    if ($deletedRows > 0) {
        return redirect()->route('riwayat-straight-line.index')->with('success', 'Riwayat berhasil dihapus.');
    } else {
        return redirect()->back()->with('error', 'Riwayat tidak ditemukan atau sudah dihapus.');
    }
}

}
