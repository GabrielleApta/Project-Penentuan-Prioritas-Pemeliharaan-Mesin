<?php

namespace App\Http\Controllers;

use App\Models\HasilSaw;
use Illuminate\Http\Request;
use App\Models\JadwalPemeliharaan;
use App\Models\Mesin;
use App\Models\PenilaianMesin;
use App\Models\Prioritas;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


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
    $periode = now()->format('Y-m'); // Contoh: 2025-07
    $jumlahMesin = 300; // total mesin yang ingin dijadwalkan (misal 10 per hari × 30 hari)
    $mesinPerHari = 10;

    // Ambil mesin berdasarkan skor SAW
    $ranking = Prioritas::with('mesin')
        ->orderBy('skor_akhir', 'asc')
        ->take($jumlahMesin)
        ->get();

    $startDate = Carbon::now()->startOfMonth();
    $counter = 0;

    foreach ($ranking as $index => $item) {
        // Cek apakah mesin sudah punya jadwal untuk periode ini
        $sudahAda = JadwalPemeliharaan::where('mesin_id', $item->mesin_id)
            ->where('periode', $periode)
            ->exists();

        if ($sudahAda) continue;

        // Tentukan tanggal jadwal: 10 mesin per hari
        $hariKe = floor($counter / $mesinPerHari);
        $tanggalJadwal = $startDate->copy()->addDays($hariKe);

        // Cek jika tanggal sudah masuk ke bulan berikutnya, skip
        if ($tanggalJadwal->format('Y-m') !== $periode) break;

        JadwalPemeliharaan::create([
            'mesin_id' => $item->mesin_id,
            'periode' => $periode,
            'tanggal_jadwal' => $tanggalJadwal->toDateString(),
            'prioritas' => $this->tentukanPrioritas($item->skor_akhir),
            'status' => 'terjadwal',
            'catatan' => 'Dibuat otomatis berdasarkan skor SAW',
        ]);

        $counter++;
    }

    return redirect()->route('jadwal.index')->with('success', "Jadwal bulan $periode berhasil digenerate.");
}

private function tentukanPrioritas($skor)
{
    if ($skor <= 0.4) {
        return 'tinggi';
    } elseif ($skor <= 0.7) {
        return 'sedang';
    } else {
        return 'rendah';
    }
}


public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:terjadwal,selesai,terlambat,batal',
        'tanggal_selesai' => 'nullable|date',
         'catatan' => 'nullable|string',
    ]);

    $jadwal = JadwalPemeliharaan::findOrFail($id);
    $jadwal->status = $request->status;
    $jadwal->catatan = $request->catatan;


    // Jika status selesai dan tanggal_selesai kosong, isi otomatis hari ini
    if ($request->status === 'selesai') {
        $jadwal->tanggal_selesai = $request->tanggal_selesai ?? now()->toDateString();
    } else {
        // Kalau bukan selesai, reset tanggal_selesai jadi null
        $jadwal->tanggal_selesai = null;
    }

    $jadwal->save();

    return redirect()->back()->with('success', 'Status jadwal berhasil diperbarui.');
}

public function cetakJadwalPDF()
{
    $jadwals = JadwalPemeliharaan::with('mesin')
        ->orderByRaw("FIELD(prioritas, 'tinggi', 'sedang', 'rendah')")
        ->orderBy('tanggal_jadwal')
        ->get();

    $periode = Carbon::now()->translatedFormat('F Y');

    // Ringkasan statistik
    $statistik = [
        'tinggi' => $jadwals->where('prioritas', 'tinggi')->count(),
        'sedang' => $jadwals->where('prioritas', 'sedang')->count(),
        'rendah' => $jadwals->where('prioritas', 'rendah')->count(),
    ];

    $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

    // ✅ BUAT URL CHART
    $chartConfig = [
        "type" => "pie",
        "data" => [
            "labels" => ["Prioritas Tinggi", "Prioritas Sedang", "Prioritas Rendah"],
            "datasets" => [[
                "data" => [$statistik['tinggi'], $statistik['sedang'], $statistik['rendah']],
                "backgroundColor" => ["#dc3545", "#ffc107", "#28a745"]
            ]]
        ],
        "options" => [
            "title" => [
                "display" => true,
                "text" => "Distribusi Prioritas Mesin"
            ]
        ]
    ];

     $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));

    // ✅ CONVERT TO BASE64
    $chartImage = base64_encode(file_get_contents($chartUrl));

    $pdf = PDF::loadView('pages.jadwal.laporan_pdf', compact('jadwals', 'periode', 'statistik', 'tanggalCetak', 'chartImage'))
        ->setPaper('a4', 'portrait');

    return $pdf->stream("Jadwal_Pemeliharaan_Periodik_{$periode}.pdf");
}
}
