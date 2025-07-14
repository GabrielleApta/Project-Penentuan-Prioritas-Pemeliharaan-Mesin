<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;

class KriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $kriteria = Kriteria::all();
        return view('pages.kriteria.index', compact('kriteria'));
    }

    public function create()
    {
        return view('pages.kriteria.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kriteria'   => 'required|string|max:255',
            'bobot'           => 'required|numeric|between:0,100',
            'jenis_kriteria'  => 'required|in:benefit,cost',
        ]);

        $validated['bobot'] = $validated['bobot'] / 100;

        Kriteria::create($validated);

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan');
    }

    public function edit(Kriteria $kriteria)
{
    return view('pages.kriteria.edit', compact('kriteria'));
}

    public function update(Request $request, Kriteria $kriteria)
{
    $validated = $request->validate([
        'nama_kriteria'   => 'required|string|max:255',
        'bobot'           => 'required|numeric|between:0,100',
        'jenis_kriteria'  => 'required|in:benefit,cost',
    ]);

    $validated['bobot'] = $validated['bobot'] / 100;
    $kriteria->update($validated);

    return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui');
}

    public function destroy(Kriteria $kriteria)
{
    $kriteria->delete();

    return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus');
}
}
