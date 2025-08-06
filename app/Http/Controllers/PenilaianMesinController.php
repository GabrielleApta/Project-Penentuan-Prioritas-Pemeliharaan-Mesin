<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\PenilaianMesin;
use App\Models\KerusakanTahunan;
use App\Exports\NormalisasiExport;
use Maatwebsite\Excel\Facades\Excel;

class PenilaianMesinController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.readonly')->only([
            'create', 'store', 'edit', 'update', 'destroy'
        ]);
    }

    // ✅ FIXED: Index dengan Raw Query dan Filter Data Mesin Hilang
    public function index()
    {
        $tahunTerakhir = 2024;

        // ✅ RAW QUERY dengan INNER JOIN (hanya ambil yang ada mesinnya)
        $penilaian = \DB::select("
            SELECT
                pm.id,
                pm.mesin_id,
                pm.akumulasi_penyusutan,
                pm.usia_mesin,
                pm.frekuensi_kerusakan,
                pm.waktu_downtime,
                pm.tahun_penilaian,
                m.nama_mesin
            FROM penilaian_mesin pm
            INNER JOIN mesin m ON pm.mesin_id = m.id
            WHERE pm.tahun_penilaian = ?
            ORDER BY pm.id
        ", [$tahunTerakhir]);

        // ✅ CEK APAKAH ADA DATA YANG MESINNYA HILANG
        $dataLengkap = \DB::select("
            SELECT COUNT(*) as total_penilaian
            FROM penilaian_mesin pm
            WHERE pm.tahun_penilaian = ?
        ", [$tahunTerakhir]);

        $totalPenilaian = $dataLengkap[0]->total_penilaian ?? 0;
        $totalDenganMesin = count($penilaian);

        // ✅ JIKA ADA DATA YANG MESINNYA HILANG, KIRIM FLAG
        $adaDataHilang = $totalPenilaian > $totalDenganMesin;

        // Convert ke collection
        $penilaian = collect($penilaian);

        return view('pages.penilaian.index', compact('penilaian', 'adaDataHilang', 'totalPenilaian', 'totalDenganMesin'));
    }

    public function create()
    {
        $mesin = Mesin::all();
        return view('pages.penilaian.create', compact('mesin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mesin_id'         => 'required|exists:mesin,id', // ✅ FIXED: table name
            'tahun_penilaian'  => 'required|integer',
        ]);

        PenilaianMesin::create($request->only('mesin_id', 'tahun_penilaian'));

        return redirect()->route('penilaian.index')->with('success', 'Data penilaian berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $data = PenilaianMesin::findOrFail($id);
        $data->delete();

        return redirect()->route('penilaian.index')->with('success', 'Data penilaian berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun_penilaian'       => 'required|integer',
            'akumulasi_penyusutan'  => 'required|numeric',
            'usia_mesin'            => 'required|numeric',
            'frekuensi_kerusakan'   => 'required|numeric',
            'waktu_downtime'        => 'required|numeric',
        ]);

        $penilaian = PenilaianMesin::findOrFail($id);
        $penilaian->update($request->all());

        return redirect()->route('penilaian.index')->with('success', 'Data penilaian berhasil diperbarui.');
    }

    public function generatePenilaian()
    {
        $mesins = Mesin::all();

        $tahunRange = [2022, 2023, 2024];
        $tahunPenilaian = 2024;

        foreach ($mesins as $mesin) {
            $penyusutanTahunan = ($mesin->harga_beli - $mesin->nilai_sisa) / max($mesin->umur_ekonomis, 1);
            $akumulasi = $penyusutanTahunan * $mesin->umur_ekonomis;
            $usia = 2025 - $mesin->tahun_pembelian;

            $kerusakanTahunan = KerusakanTahunan::where('mesin_id', $mesin->id)
                ->whereIn('tahun', $tahunRange)
                ->get();

            $jumlahTahunAktif = $kerusakanTahunan->pluck('tahun')->unique()->count();

            $totalSkorKerusakan = $kerusakanTahunan->sum(function ($item) {
                return ($item->kerusakan_parah * 3) + ($item->kerusakan_ringan * 1);
            });

            $totalSkorDowntime = $kerusakanTahunan->sum(function ($item) {
                return ($item->downtime_parah * 3) + ($item->downtime_ringan * 1);
            });

            $rataKerusakan = $jumlahTahunAktif > 0 ? $totalSkorKerusakan / $jumlahTahunAktif : 0;
            $rataDowntime  = $jumlahTahunAktif > 0 ? $totalSkorDowntime / $jumlahTahunAktif : 0;

            PenilaianMesin::updateOrCreate(
                [
                    'mesin_id' => $mesin->id,
                    'tahun_penilaian' => $tahunPenilaian,
                ],
                [
                    'akumulasi_penyusutan' => $akumulasi,
                    'usia_mesin'           => $usia,
                    'frekuensi_kerusakan'  => $rataKerusakan,
                    'waktu_downtime'       => $rataDowntime,
                ]
            );
        }

        return redirect()->back()->with('success', 'Penilaian berhasil digenerate!');
    }

    // ✅ FIXED: Normalisasi juga gunakan raw query untuk konsistensi
    public function normalisasi()
    {
        $tahunTerakhir = 2024;

        // ✅ RAW QUERY untuk data normalisasi
        $data = \DB::select("
            SELECT
                pm.id,
                pm.mesin_id,
                pm.akumulasi_penyusutan,
                pm.usia_mesin,
                pm.frekuensi_kerusakan,
                pm.waktu_downtime,
                pm.tahun_penilaian,
                m.nama_mesin
            FROM penilaian_mesin pm
            INNER JOIN mesin m ON pm.mesin_id = m.id
            WHERE pm.tahun_penilaian = ?
            ORDER BY pm.id
        ", [$tahunTerakhir]);

        if (empty($data)) {
            return redirect()->back()->with('error', 'Data penilaian tidak ditemukan untuk tahun ' . $tahunTerakhir);
        }

        // Convert to collection for easier manipulation
        $data = collect($data);

        // Ambil nilai minimum (karena semua kriteria adalah cost → nilai kecil lebih baik)
        $minPenyusutan = $data->min('akumulasi_penyusutan') ?: 0.0001;
        $minUsia       = $data->min('usia_mesin') ?: 0.0001;
        $minFrekuensi  = $data->min('frekuensi_kerusakan') ?: 0.0001;
        $minDowntime   = $data->min('waktu_downtime') ?: 0.0001;

        $normalisasi = [];

        foreach ($data as $item) {
            // Pastikan tidak membagi dengan 0
            $normPenyusutan = $item->akumulasi_penyusutan > 0 ? $minPenyusutan / $item->akumulasi_penyusutan : 0;
            $normUsia       = $item->usia_mesin > 0 ? $minUsia / $item->usia_mesin : 0;
            $normFrekuensi  = $item->frekuensi_kerusakan > 0 ? $minFrekuensi / $item->frekuensi_kerusakan : 0;
            $normDowntime   = $item->waktu_downtime > 0 ? $minDowntime / $item->waktu_downtime : 0;

            // Hitung skor akhir SAW
            $skor =
                ($normPenyusutan * 0.30) +
                ($normUsia * 0.30) +
                ($normFrekuensi * 0.20) +
                ($normDowntime * 0.20);

            $normalisasi[] = [
                'mesin'           => $item->nama_mesin,
                'norm_penyusutan' => number_format($normPenyusutan, 10, '.', ''),
                'norm_usia'       => number_format($normUsia, 10, '.', ''),
                'norm_frekuensi'  => number_format($normFrekuensi, 10, '.', ''),
                'norm_downtime'   => number_format($normDowntime, 10, '.', ''),
                'skor_akhir'      => number_format($skor, 10, '.', ''),
            ];
        }

        return view('pages.penilaian.normalisasi', compact('normalisasi'));
    }

    public function exportExcel()
    {
        return Excel::download(new NormalisasiExport, 'normalisasi_penilaian.xlsx');
    }
}
