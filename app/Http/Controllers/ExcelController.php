<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\MesinImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function showForm()
    {
        return view('upload');
    }

 /*   public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        // Simpan file ke storage/app/public/uploads
        $path = $request->file('file')->store('uploads', 'public');

        return back()->with('success', 'File berhasil diupload dan diproses.');
    }
        */
    public function uploadFile(Request $request)
{

    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    Excel::import(new MesinImport, $request->file('file'));

    return back()->with('success', 'Data dari file Excel berhasil diimport ke database!');
}
}
