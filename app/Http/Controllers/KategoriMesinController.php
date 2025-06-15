<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriMesin;

class KategoriMesinController extends Controller
{
    public function index()
    {
        $kategoriMesin = KategoriMesin::select('nama_kategori as kategori')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('nama_kategori')
            ->get();

        return response()->json($kategoriMesin);
    }
}
