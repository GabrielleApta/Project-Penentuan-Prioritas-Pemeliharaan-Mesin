<?php

namespace App\Http\Controllers;

use App\Models\KerusakanTahunan;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\KerusakanTahunanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\KerusakanTahunanExport;

class KerusakanTahunanController extends Controller
{
    public function index()
    {
        // Hanya tampilkan data yang mesinnya masih ada
        $data = KerusakanTahunan::with('mesin')
            ->whereHas('mesin') // Filter hanya yang punya relasi mesin
            ->latest()
            ->get();

        return view('pages.kerusakan_tahunan.index', compact('data'));
    }

    public function create()
    {
        $mesin = Mesin::all();
        return view('pages.kerusakan_tahunan.create', compact('mesin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mesin_id'         => 'required|exists:mesin,id',
            'tahun'            => [
                'required', 'integer', 'min:2000', 'max:' . date('Y'),
                Rule::unique('kerusakan_tahunan')->where(function ($query) use ($request) {
                    return $query->where('mesin_id', $request->mesin_id);
                })
            ],
            'kerusakan_ringan' => 'required|integer|min:0',
            'kerusakan_parah'  => 'required|integer|min:0',
            'downtime_ringan'  => 'required|numeric|min:0',
            'downtime_parah'   => 'required|numeric|min:0',
        ]);

        $skorFrekuensi = ($request->kerusakan_parah * 3) + $request->kerusakan_ringan;
        $skorDowntime  = ($request->downtime_parah * 3) + $request->downtime_ringan;

        KerusakanTahunan::create([
            'mesin_id'                 => $request->mesin_id,
            'tahun'                    => $request->tahun,
            'kerusakan_ringan'         => $request->kerusakan_ringan,
            'kerusakan_parah'          => $request->kerusakan_parah,
            'downtime_ringan'          => $request->downtime_ringan,
            'downtime_parah'           => $request->downtime_parah,
        ]);

        return redirect()->route('kerusakan-tahunan.index')->with('success', 'Data kerusakan tahunan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data  = KerusakanTahunan::findOrFail($id);
        $mesin = Mesin::all();
        return view('pages.kerusakan_tahunan.edit', compact('data', 'mesin'));
    }

    public function update(Request $request, $id)
    {
        $data = KerusakanTahunan::findOrFail($id);

        $request->validate([
            'mesin_id'         => 'required|exists:mesin,id',
            'tahun'            => [
                'required', 'integer', 'min:2000', 'max:' . date('Y'),
                Rule::unique('kerusakan_tahunan')->where(function ($query) use ($request) {
                    return $query->where('mesin_id', $request->mesin_id);
                })->ignore($data->id)
            ],
            'kerusakan_ringan' => 'required|integer|min:0',
            'kerusakan_parah'  => 'required|integer|min:0',
            'downtime_ringan'  => 'required|numeric|min:0',
            'downtime_parah'   => 'required|numeric|min:0',
        ]);

        $skorFrekuensi = ($request->kerusakan_parah * 3) + $request->kerusakan_ringan;
        $skorDowntime  = ($request->downtime_parah * 3) + $request->downtime_ringan;

        $data->update([
            'mesin_id'                 => $request->mesin_id,
            'tahun'                    => $request->tahun,
            'kerusakan_ringan'         => $request->kerusakan_ringan,
            'kerusakan_parah'          => $request->kerusakan_parah,
            'downtime_ringan'          => $request->downtime_ringan,
            'downtime_parah'           => $request->downtime_parah,
        ]);

        return redirect()->route('kerusakan-tahunan.index')->with('success', 'Data kerusakan tahunan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $data = KerusakanTahunan::findOrFail($id);
        $data->delete();

        return redirect()->route('kerusakan-tahunan.index')->with('success', 'Data kerusakan tahunan berhasil dihapus');
    }

    public function rataRataSkor()
    {
        $tahun_terakhir = [2022, 2023, 2024];

        $mesin = Mesin::with(['kerusakanTahunan' => function ($query) use ($tahun_terakhir) {
            $query->whereIn('tahun', $tahun_terakhir);
        }])->get();

        $hasil = [];

        foreach ($mesin as $m) {
            $kerusakan = $m->kerusakanTahunan;
            $jumlah    = $kerusakan->count();

            $hasil[] = [
                'nama_mesin'     => $m->nama_mesin,
                'rata_frekuensi' => $jumlah > 0 ? round($kerusakan->avg('skor_frekuensi_kerusakan'), 2) : 0,
                'rata_downtime'  => $jumlah > 0 ? round($kerusakan->avg('skor_waktu_downtime'), 2) : 0,
            ];
        }

        return view('pages.kerusakan_tahunan.rata-rata', compact('hasil'));
    }

    public function showImportForm()
    {
        return view('kerusakan.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv'
        ]);

        try {
            Excel::import(new KerusakanTahunanImport, $request->file('file'));
            return redirect()->back()->with('import_success', 'Data berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function exportPdfFiltered(Request $request)
    {
        $request->validate([
            'tahun' => 'nullable|integer',
            'mesin_id' => 'nullable|exists:mesin,id',
        ]);

        // Hanya tampilkan data yang mesinnya masih ada
        $query = KerusakanTahunan::with('mesin')->whereHas('mesin');
        $namaMesin = null;

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('mesin_id')) {
            $query->where('mesin_id', $request->mesin_id);
            $mesin = Mesin::find($request->mesin_id);
            $namaMesin = $mesin ? $mesin->nama_mesin : null;
        }

        $data = $query->get();

        // Generate Nama File
        $filename = 'Laporan Kerusakan';

        if ($request->filled('tahun') && $namaMesin) {
            $filename .= ' ' . Str::slug($namaMesin, '_') . ' Tahun ' . $request->tahun;
        } elseif ($request->filled('tahun')) {
            $filename .= ' Tahun ' . $request->tahun;
        } elseif ($namaMesin) {
            $filename .= ' ' . Str::slug($namaMesin, '_');
        }

        $filename .= '.pdf';

        // Load PDF view
        $pdf = Pdf::loadView('pages.kerusakan_tahunan.pdf_filtered', [
            'data' => $data,
            'tahun' => $request->tahun,
            'namaMesin' => $namaMesin,
            'pdf' => true,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    public function exportExcel()
    {
        return Excel::download(new KerusakanTahunanExport, 'kerusakan_tahunan.xlsx');
    }

    // Method untuk cleanup data orphan (opsional)
    public function cleanupOrphanData()
    {
        $deletedCount = KerusakanTahunan::whereDoesntHave('mesin')->delete();

        return redirect()->route('kerusakan-tahunan.index')
            ->with('success', "Berhasil membersihkan {$deletedCount} data yang tidak valid");
    }
}
