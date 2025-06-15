<?php

namespace App\Http\Controllers;

use App\Models\KerusakanTahunan;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\KerusakanTahunanImport;
use Maatwebsite\Excel\Facades\Excel;

class KerusakanTahunanController extends Controller
{
    public function index()
    {
        $data = KerusakanTahunan::with('mesin')->latest()->get();
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

}
