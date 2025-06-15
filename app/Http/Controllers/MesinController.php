<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Depresiasi;
use App\Models\PenilaianMesin;
use App\Models\Kriteria;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MesinImport;
use App\Exports\MesinExport;
use Barryvdh\DomPDF\Facade\Pdf;

class MesinController extends Controller
{
    public function __construct()
    {
    $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $mesin = Mesin::when($status, function ($query) use ($status) {
        return $query->where('status', $status);
        })->get();

        return view('pages.mesin.index', compact('mesin'));

        $mesin = Mesin::all();
        return view('pages.mesin.index', compact('mesin'));
    }

    public function create()
    {
        return view('pages.mesin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mesin'      => 'required|string|max:255',
            'kode_mesin'      => 'required|string|unique:mesin,kode_mesin|max:50',
            'harga_beli'      => 'required|numeric|min:0',
            'tahun_pembelian' => 'required|integer|min:0',
            'spesifikasi_mesin' => 'required|string|max:255',
            'daya_motor'      => 'required|numeric|min:0',
            'lokasi_mesin'    => 'required|string|max:255',
            'nilai_sisa'      => 'required|numeric|min:0',
            'umur_ekonomis'   => 'required|integer|min:1',
            'akumulasi_penyusutan'=> 'required|numeric|min:0'
        ]);

        // Simpan data mesin
        $mesin = Mesin::create($request->only([
            'nama_mesin', 'kode_mesin', 'harga_beli', 'tahun_pembelian',
            'spesifikasi_mesin', 'daya_motor', 'lokasi_mesin', 'nilai_sisa', 'umur_ekonomis'
        ]));

        // 🔹 Hitung depresiasi tahunan
        $harga_beli = (float) $mesin->harga_beli;
        $nilai_sisa = (float) $mesin->nilai_sisa;
        $umur_ekonomis = (int) $mesin->umur_ekonomis;

        if ($umur_ekonomis > 0) {
            $depresiasi_tahunan = ($harga_beli - $nilai_sisa) / $umur_ekonomis;

            for ($tahun = 1; $tahun <= $umur_ekonomis; $tahun++) {
                Depresiasi::create([
                    'mesin_id'   => $mesin->id,
                    'tahun'      => now()->year + $tahun - 1,
                    'nilai_buku' => max($harga_beli - ($depresiasi_tahunan * $tahun), $nilai_sisa),
                ]);
            }
        }

        // 🔹 Otomatis Tambahkan Data ke `penilaian_mesin`
        foreach (Kriteria::all() as $k) {
            PenilaianMesin::create([
                'mesin_id'    => $mesin->id,
                'kriteria_id' => $k->id,
                'nilai'       => 0,
            ]);
        }

        return redirect()->route('mesin.index')->with('success', 'Mesin berhasil ditambahkan!');
    }

    public function edit(Mesin $mesin)
    {
        return view('pages.mesin.edit', compact('mesin'));
    }

    public function update(Request $request, Mesin $mesin)
    {
        $request->validate([
            'nama_mesin'      => 'required|string|max:255',
            'kode_mesin'      => 'required|string|unique:mesin,kode_mesin,' . $mesin->id . '|max:50',
            'harga_beli'      => 'required|numeric|min:0',
            'tahun_pembelian' => 'required|integer|min:0',
            'spesifikasi_mesin' => 'required|string|max:255',
            'daya_motor'      => 'required|numeric|min:0',
            'lokasi_mesin'    => 'required|string|max:255',
            'nilai_sisa'      => 'required|numeric|min:0',
            'umur_ekonomis'   => 'required|integer|min:1',
        ]);

        $mesin->update($request->only([
            'nama_mesin', 'kode_mesin', 'harga_beli', 'tahun_pembelian',
            'spesifikasi_mesin', 'daya_motor', 'lokasi_mesin', 'nilai_sisa', 'umur_ekonomis'
        ]));

        return redirect()->route('mesin.index')->with('success', 'Mesin berhasil diperbarui!');
    }

    public function destroy(Mesin $mesin)
    {
        // 🔹 Hapus data terkait sebelum menghapus mesin
        Depresiasi::where('mesin_id', $mesin->id)->delete();
        PenilaianMesin::where('mesin_id', $mesin->id)->delete();
        $mesin->delete();

        return redirect()->route('mesin.index')->with('success', 'Mesin berhasil dihapus!');
    }


public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    try {
        Excel::import(new MesinImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data mesin berhasil diimport.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
    }
}


    public function exportExcel()
    {
        return Excel::download(new MesinExport(), 'data_mesin.xlsx');
    }

    public function exportPDF()
    {
        $mesin = Mesin::all();
        $pdf = Pdf::loadView('pages.mesin.mesin_pdf', compact('mesin'))
                  ->setPaper('a4', 'landscape');
        return $pdf->stream('Data_Mesin_'.date('d-m-Y').'.pdf');
    }

    public function samjin()
{
    $mesin = Mesin::where('nama_mesin', 'Samjin')->get();
    return view('mesin.index', compact('mesin'));
}

public function twisting()
{
    $mesin = Mesin::where('nama_mesin', 'Twisting')->get();
    return view('mesin.index', compact('mesin'));
}

public function aktif()
{
    $mesin = Mesin::where('status', 'aktif')->get();
    return view('mesin.index', compact('mesin'));
}

public function tidakaktif()
{
    $mesin = Mesin::where('status', 'tidakaktif')->get();
    return view('mesin.index', compact('mesin'));
}

}
