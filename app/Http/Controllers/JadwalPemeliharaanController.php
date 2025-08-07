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
    public function index()
    {
        // ✅ Hapus jadwal yang mesinnya sudah tidak ada (orphaned data)
        JadwalPemeliharaan::whereDoesntHave('mesin')->delete();

        // ✅ Ambil jadwal yang valid (yang mesinnya masih ada)
        $jadwals = JadwalPemeliharaan::with('mesin')
            ->whereHas('mesin') // Pastikan mesin masih ada
            ->orderBy('tanggal_jadwal', 'asc')
            ->get();

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

        // ✅ Cek apakah ada data mesin
        if (Mesin::count() == 0) {
            return redirect()->route('jadwal.index')->with('error', 'Tidak ada data mesin untuk dijadwalkan. Silakan tambah data mesin terlebih dahulu.');
        }

        // ✅ Cek apakah ada data prioritas SAW
        if (Prioritas::count() == 0) {
            return redirect()->route('jadwal.index')->with('error', 'Belum ada data hasil perhitungan SAW. Silakan lakukan perhitungan SAW terlebih dahulu.');
        }

        // Ambil mesin berdasarkan skor SAW
        $ranking = Prioritas::with('mesin')
            ->whereHas('mesin') // ✅ Pastikan mesin masih ada
            ->orderBy('skor_akhir', 'asc')
            ->take($jumlahMesin)
            ->get();

        if ($ranking->isEmpty()) {
            return redirect()->route('jadwal.index')->with('error', 'Tidak ada data ranking SAW yang valid untuk generate jadwal.');
        }

        $startDate = Carbon::now()->startOfMonth();
        $counter = 0;
        $berhasilDibuat = 0;

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
            $berhasilDibuat++;
        }

        if ($berhasilDibuat > 0) {
            return redirect()->route('jadwal.index')->with('success', "Berhasil membuat $berhasilDibuat jadwal untuk periode $periode.");
        } else {
            return redirect()->route('jadwal.index')->with('info', 'Semua mesin sudah memiliki jadwal untuk periode ini.');
        }
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

        // ✅ Cek apakah mesin masih ada
        if (!$jadwal->mesin) {
            return redirect()->back()->with('error', 'Data mesin untuk jadwal ini tidak ditemukan. Jadwal akan dihapus.');
        }

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
        // ✅ Hapus jadwal orphan sebelum cetak
        JadwalPemeliharaan::whereDoesntHave('mesin')->delete();

        $jadwals = JadwalPemeliharaan::with('mesin')
            ->whereHas('mesin') // ✅ Pastikan mesin masih ada
            ->orderByRaw("FIELD(prioritas, 'tinggi', 'sedang', 'rendah')")
            ->orderBy('tanggal_jadwal')
            ->get();

        if ($jadwals->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data jadwal yang valid untuk dicetak.');
        }

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

    // ✅ Tambahkan method untuk membersihkan data orphan
    public function cleanOrphanData()
    {
        $deletedCount = JadwalPemeliharaan::whereDoesntHave('mesin')->count();
        JadwalPemeliharaan::whereDoesntHave('mesin')->delete();

        return redirect()->route('jadwal.index')->with('success', "Berhasil menghapus $deletedCount data jadwal yang tidak valid.");
    }
}
