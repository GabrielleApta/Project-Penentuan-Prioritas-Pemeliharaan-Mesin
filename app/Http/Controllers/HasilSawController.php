<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilSaw;
use App\Models\Mesin;
use App\Models\Kriteria;
use App\Models\PenilaianMesin;

class HasilSawController extends Controller
{
    public function __construct()
    {
    $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $hasil_saw = HasilSaw::with('mesin')->orderBy('ranking', 'asc')->get();
        return view('pages.hasil_saw.index', compact('hasil_saw'));
    }

    public function hitungSAW()
    {
        $mesinList = Mesin::all();
        $kriteriaList = Kriteria::all();
        $penilaian = PenilaianMesin::all();

        // Normalisasi nilai kriteria
        $normalisasi = [];
        foreach ($kriteriaList as $kriteria) {
            $maxValue = PenilaianMesin::where('kriteria_id', $kriteria->id)->max('nilai');
            foreach ($penilaian->where('kriteria_id', $kriteria->id) as $nilai) {
                $normalisasi[$nilai->mesin_id][$kriteria->id] = $nilai->nilai / $maxValue;
            }
        }

        // Hitung skor SAW
        $hasil_saw = [];
        foreach ($mesinList as $mesin) {
            $skor = 0;
            foreach ($kriteriaList as $kriteria) {
                $skor += ($normalisasi[$mesin->id][$kriteria->id] ?? 0) * $kriteria->bobot;
            }
            $hasil_saw[] = [
                'mesin_id' => $mesin->id,
                'skor_akhir' => $skor
            ];
        }

        // Simpan hasil ke database
        HasilSaw::truncate();
        usort($hasil_saw, fn($a, $b) => $b['skor_akhir'] <=> $a['skor_akhir']);

        foreach ($hasil_saw as $index => $data) {
            HasilSaw::create([
                'mesin_id' => $data['mesin_id'],
                'skor_akhir' => $data['skor_akhir'],
                'ranking' => $index + 1
            ]);
        }

        return redirect()->route('hasil_saw.index')->with('success', 'Perhitungan SAW berhasil dilakukan!');
    }
}
