<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\PenilaianMesin;
use App\Models\KerusakanTahunan;
use App\Services\NormalisasiService; // ✅ IMPORT SERVICE
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

        // ✅ RAW QUERY dengan INNER JOIN dan CUSTOM SORTING
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
            ORDER BY
                CASE
                    WHEN m.nama_mesin LIKE 'Samjin%' THEN 1
                    WHEN m.nama_mesin LIKE 'Twisting%' THEN 2
                    WHEN m.nama_mesin LIKE 'Winder%' THEN 3
                    WHEN m.nama_mesin LIKE 'Quick Traverse%' THEN 4
                    WHEN m.nama_mesin LIKE 'Gulungan%' THEN 5
                    WHEN m.nama_mesin LIKE 'Winding%' THEN 6
                    WHEN m.nama_mesin LIKE 'Kamitsu%' THEN 7
                    WHEN m.nama_mesin LIKE 'Vacum%' THEN 8
                    ELSE 9
                END,
                CAST(REGEXP_REPLACE(m.nama_mesin, '[^0-9]', '') AS UNSIGNED),
                m.nama_mesin
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

    // ✅ CLEAN: Gunakan Service untuk normalisasi
    public function normalisasi()
    {
        $tahunTerakhir = 2024;

        // ✅ RAW QUERY untuk data normalisasi dengan CUSTOM SORTING
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
            ORDER BY
                CASE
                    WHEN m.nama_mesin LIKE 'Samjin%' THEN 1
                    WHEN m.nama_mesin LIKE 'Twisting%' THEN 2
                    WHEN m.nama_mesin LIKE 'Winder%' THEN 3
                    WHEN m.nama_mesin LIKE 'Quick Traverse%' THEN 4
                    WHEN m.nama_mesin LIKE 'Gulungan%' THEN 5
                    WHEN m.nama_mesin LIKE 'Winding%' THEN 6
                    WHEN m.nama_mesin LIKE 'Kamitsu%' THEN 7
                    WHEN m.nama_mesin LIKE 'Vacum%' THEN 8
                    ELSE 9
                END,
                CAST(REGEXP_REPLACE(m.nama_mesin, '[^0-9]', '') AS UNSIGNED),
                m.nama_mesin
        ", [$tahunTerakhir]);

        if (empty($data)) {
            return redirect()->back()->with('error', 'Data penilaian tidak ditemukan untuk tahun ' . $tahunTerakhir);
        }

        // ✅ GUNAKAN SERVICE UNTUK NORMALISASI
        $normalisasi = NormalisasiService::hitungNormalisasiForView($data);

        return view('pages.penilaian.normalisasi', compact('normalisasi'));
    }

    public function exportExcel()
    {
        return Excel::download(new NormalisasiExport, 'normalisasi_penilaian.xlsx');
    }
}
