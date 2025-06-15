<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Mesin;
use App\Models\KerusakanTahunan;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index()
    {
        $penilaian = Penilaian::with('mesin')->orderBy('tahun', 'desc')->get();
        return view('pages.penilaian.index', compact('penilaian'));
    }

    public function generate()
    {
        // Nanti diisi logika generate otomatis semua nilai SAW
        return redirect()->back()->with('success', 'Generate penilaian berhasil!');
    }
}
