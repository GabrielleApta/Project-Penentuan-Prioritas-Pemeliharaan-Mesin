<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Prioritas;
use App\Models\PenilaianMesin;
use App\Services\NormalisasiService; // ✅ IMPORT SERVICE
use Illuminate\Support\Facades\DB;
use PDF;

class PrioritasController extends Controller
{
    protected $tahun = 2024;

    public function __construct()
    {
        $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        // Hanya tampilkan data yang mesinnya masih ada
        $hasil_saw = Prioritas::with('mesin')
            ->whereHas('mesin') // Filter hanya yang punya relasi mesin
            ->orderBy('skor_akhir')
            ->get();

        return view('pages.prioritas.index', compact('hasil_saw'));
    }

    public function hitungSAW()
    {
        // Hanya ambil penilaian yang mesinnya masih ada - dengan CUSTOM SORTING
        $penilaian = PenilaianMesin::with('mesin')
            ->whereHas('mesin') // Filter hanya yang punya relasi mesin
            ->where('tahun_penilaian', $this->tahun)
            ->get()
            ->sortBy(function ($item) {
                $nama = $item->mesin->nama_mesin;

                // Tentukan prioritas kategori
                if (strpos($nama, 'Samjin') === 0) $kategori = 1;
                elseif (strpos($nama, 'Twisting') === 0) $kategori = 2;
                elseif (strpos($nama, 'Winder') === 0) $kategori = 3;
                elseif (strpos($nama, 'Quick Traverse') === 0) $kategori = 4;
                elseif (strpos($nama, 'Gulungan') === 0) $kategori = 5;
                elseif (strpos($nama, 'Winding') === 0) $kategori = 6;
                elseif (strpos($nama, 'Kamitsu') === 0) $kategori = 7;
                elseif (strpos($nama, 'Vacum') === 0) $kategori = 8;
                else $kategori = 9;

                // Extract nomor dari nama mesin
                preg_match('/\d+/', $nama, $matches);
                $nomor = isset($matches[0]) ? (int)$matches[0] : 0;

                // Return sorting key: kategori + nomor (padded) + nama
                return sprintf('%d_%05d_%s', $kategori, $nomor, $nama);
            })
            ->values(); // Reset array keys

        if ($penilaian->isEmpty()) {
            return back()->with('error', "Data penilaian tahun {$this->tahun} belum tersedia atau semua mesin sudah dihapus.");
        }

        // Siapkan data untuk normalisasi - PENTING: sort by mesin_id untuk konsistensi
        $data_nilai = $penilaian->sortBy('mesin_id')->map(fn($item) => [
            'mesin_id'             => $item->mesin_id,
            'akumulasi_penyusutan' => $item->akumulasi_penyusutan,
            'usia_mesin'           => $item->usia_mesin,
            'frekuensi_kerusakan'  => $item->frekuensi_kerusakan,
            'waktu_downtime'       => $item->waktu_downtime,
        ])->values()->toArray(); // values() untuk reset array keys

        // ✅ GUNAKAN SERVICE UNTUK NORMALISASI
        $normalisasi = NormalisasiService::hitungNormalisasi($data_nilai);
        $hasil = NormalisasiService::hitungSkorAkhir($normalisasi);

        // Urutkan & ranking (semua cost → ASC, nilai kecil = ranking baik)
        usort($hasil, fn($a, $b) => $a['skor_akhir'] <=> $b['skor_akhir']);
        foreach ($hasil as $i => &$row) {
            $row['rangking'] = $i + 1;
        }

        // Simpan ke database
        DB::table('hasil_saw')->truncate();
        Prioritas::insert($hasil);

        // ====== Tambahan: Simpan ke tabel riwayat_saw ======
        $kode_perhitungan = 'SAW-' . now()->format('Y') . '-' . str_pad(\App\Models\RiwayatSaw::count() + 1, 3, '0', STR_PAD_LEFT);

        foreach ($hasil as $row) {
            $p = $penilaian->firstWhere('mesin_id', $row['mesin_id']);
            if (!$p || !$p->mesin) continue; // Skip jika mesin tidak ada

            \App\Models\RiwayatSaw::create([
                'mesin_id'             => $p->mesin->id,
                'kode_perhitungan'     => $kode_perhitungan,
                'nama_mesin'           => $p->mesin->nama_mesin,
                'akumulasi_penyusutan' => $p->akumulasi_penyusutan,
                'usia_mesin'           => $p->usia_mesin,
                'frekuensi_kerusakan'  => $p->frekuensi_kerusakan,
                'waktu_downtime'       => $p->waktu_downtime,
                'skor_akhir'           => $row['skor_akhir'],
                'ranking'              => $row['rangking'],
            ]);
        }

        return redirect()->route('prioritas.index')->with('success', 'Perhitungan SAW berhasil dilakukan.');
    }

    public function detailSAW($mesin_id)
    {
        $penilaian = PenilaianMesin::with('mesin')
            ->where('mesin_id', $mesin_id)
            ->where('tahun_penilaian', $this->tahun)
            ->whereHas('mesin') // Pastikan mesinnya masih ada
            ->firstOrFail();

        $data = $penilaian->only(NormalisasiService::getNamaKriteria());
        $data['mesin_id'] = $mesin_id;

        // Hanya ambil data yang mesinnya masih ada untuk perhitungan normalisasi - dengan CUSTOM SORTING
        $semua = PenilaianMesin::whereHas('mesin')
            ->where('tahun_penilaian', $this->tahun)
            ->with('mesin')
            ->get()
            ->sortBy(function ($p) {
                $nama = $p->mesin->nama_mesin;

                // Tentukan prioritas kategori
                if (strpos($nama, 'Samjin') === 0) $kategori = 1;
                elseif (strpos($nama, 'Twisting') === 0) $kategori = 2;
                elseif (strpos($nama, 'Winder') === 0) $kategori = 3;
                elseif (strpos($nama, 'Quick Traverse') === 0) $kategori = 4;
                elseif (strpos($nama, 'Gulungan') === 0) $kategori = 5;
                elseif (strpos($nama, 'Winding') === 0) $kategori = 6;
                elseif (strpos($nama, 'Kamitsu') === 0) $kategori = 7;
                elseif (strpos($nama, 'Vacum') === 0) $kategori = 8;
                else $kategori = 9;

                // Extract nomor dari nama mesin
                preg_match('/\d+/', $nama, $matches);
                $nomor = isset($matches[0]) ? (int)$matches[0] : 0;

                return sprintf('%d_%05d_%s', $kategori, $nomor, $nama);
            })
            ->map(fn($p) => [
                'mesin_id'             => $p->mesin_id,
                'akumulasi_penyusutan' => $p->akumulasi_penyusutan,
                'usia_mesin'           => $p->usia_mesin,
                'frekuensi_kerusakan'  => $p->frekuensi_kerusakan,
                'waktu_downtime'       => $p->waktu_downtime,
            ])->values()->toArray(); // values() untuk reset array keys

        // ✅ GUNAKAN SERVICE
        $normalisasiData = NormalisasiService::hitungNormalisasi($semua);
        $normal = collect($normalisasiData)->firstWhere('mesin_id', $mesin_id);

        $hasilSkor = NormalisasiService::hitungSkorAkhir([$normal]);
        $skor_akhir = $hasilSkor[0]['skor_akhir'];

        return view('pages.prioritas.detail', [
            'mesin'        => $penilaian->mesin,
            'data'         => $data,
            'normalisasi'  => $normal,
            'skor_akhir'   => $skor_akhir,
            'kriteria'     => NormalisasiService::$kriteria, // ✅ PASS KRITERIA KE VIEW
        ]);
    }

    public function printPDF()
    {
        // Hanya tampilkan data yang mesinnya masih ada
        $hasil_saw = Prioritas::with('mesin')
            ->whereHas('mesin')
            ->orderBy('rangking')
            ->get();

        $pdf = PDF::loadView('pages.prioritas.printPDF', compact('hasil_saw'));
        return $pdf->stream('hasil_saw.pdf');
    }

    public function detailPDF($mesin_id)
    {
        return $this->cetakDetail($mesin_id, true);
    }

    private function cetakDetail($mesin_id, $pdf = false)
    {
        $penilaian = PenilaianMesin::with('mesin')
            ->where('mesin_id', $mesin_id)
            ->where('tahun_penilaian', $this->tahun)
            ->whereHas('mesin') // Pastikan mesinnya masih ada
            ->firstOrFail();

        $data = $penilaian->only(NormalisasiService::getNamaKriteria());
        $data['mesin_id'] = $mesin_id;

        // Hanya ambil data yang mesinnya masih ada
        $semua = PenilaianMesin::whereHas('mesin')
            ->where('tahun_penilaian', $this->tahun)
            ->get()
            ->map(fn($p) => [
                'mesin_id'             => $p->mesin_id,
                'akumulasi_penyusutan' => $p->akumulasi_penyusutan,
                'usia_mesin'           => $p->usia_mesin,
                'frekuensi_kerusakan'  => $p->frekuensi_kerusakan,
                'waktu_downtime'       => $p->waktu_downtime,
            ])->toArray();

        // ✅ GUNAKAN SERVICE
        $normalisasiData = NormalisasiService::hitungNormalisasi($semua);
        $normal = collect($normalisasiData)->firstWhere('mesin_id', $mesin_id);

        $hasilSkor = NormalisasiService::hitungSkorAkhir([$normal]);
        $skor_akhir = $hasilSkor[0]['skor_akhir'];

        $viewData = [
            'mesin'       => $penilaian->mesin,
            'data'        => $data,
            'normalisasi' => $normal,
            'skor_akhir'  => $skor_akhir,
            'kriteria'    => NormalisasiService::$kriteria,
            'pdf'         => $pdf,
        ];

        return $pdf
            ? PDF::loadView('pages.prioritas.detailPDF', $viewData)->stream("detail_saw_{$mesin_id}.pdf")
            : view('pages.prioritas.detail', $viewData);
    }

    // ✅ HAPUS FUNGSI NORMALISASI() YANG LAMA - UDAH PAKE SERVICE

    // Method untuk cleanup data prioritas orphan (opsional)
    public function cleanupOrphanData()
    {
        $deletedCount = Prioritas::whereDoesntHave('mesin')->delete();

        return redirect()->route('prioritas.index')
            ->with('success', "Berhasil membersihkan {$deletedCount} data prioritas yang tidak valid");
    }
}
