<?php

namespace App\Http\Controllers;

use App\Models\HasilSaw;
use Illuminate\Http\Request;
use App\Models\JadwalPemeliharaan;
use App\Models\Mesin;
use App\Models\PenilaianMesin;
use Carbon\Carbon;

class JadwalPemeliharaanController extends Controller
{
    //
     public function index()
    {
        $jadwals = JadwalPemeliharaan::with('mesin')->orderBy('tanggal_jadwal', 'asc')->get();
        return view('pages.jadwal.index', compact('jadwals'));
    }

    public function create()
    {
        $mesins = Mesin::all();
        return view('pages.jadwal.create', compact('mesins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|exists:mesins,id',
            'tanggal_jadwal' => 'required|date',
            'prioritas' => 'required|in:tinggi,sedang,rendah',
            'catatan' => 'nullable|string',
        ]);

        JadwalPemeliharaan::create([
            'mesin_id' => $request->mesin_id,
            'tanggal_jadwal' => $request->tanggal_jadwal,
            'prioritas' => $request->prioritas,
            'catatan' => $request->catatan,
            'status' => 'terjadwal',
        ]);

         return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function generateDariSAW()
{
    $tahun = now()->year;
    $jumlahMesin = 10; // ambil 10 mesin skor terendah

    $ranking = HasilSaw::with('mesin')
        ->orderBy('skor_akhir', 'desc')
        ->take($jumlahMesin)
        ->get();

    foreach ($ranking as $index => $item) {
        JadwalPemeliharaan::updateOrCreate(
            [
                'mesin_id' => $item->mesin_id,
                'tanggal_jadwal' => Carbon::now()->addDays($index),
            ],
            [
                'prioritas' => $this->tentukanPrioritas($item->skor_akhir),
                'status' => 'terjadwal',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil digenerate otomatis berdasarkan skor SAW.');
}

private function tentukanPrioritas($skor)
{
    if ($skor <= 0.8) return 'tinggi';
    if ($skor <= 0.6) return 'sedang';
    return 'rendah';
}

}
