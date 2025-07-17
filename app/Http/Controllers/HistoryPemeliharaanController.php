<?php

namespace App\Http\Controllers;

use App\Models\HistoryPemeliharaan;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class HistoryPemeliharaanController extends Controller
{
    public function index()
    {
        $mesins = Mesin::all();
        $histories = HistoryPemeliharaan::with('mesin')->latest()->get();
        return view('pages.history.index', compact('histories', 'mesins'));
    }

    public function create()
    {
        $mesins = Mesin::all();
        return view('pages.history.create', compact('mesins'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'mesin_id' => 'required|exists:mesin,id',
        'tanggal' => 'required|date',
        'jenis_pemeliharaan' => ['required', Rule::in(['preventive', 'corrective'])],
        'deskripsi' => 'nullable|string',
        'durasi_jam' => 'nullable|numeric|min:0',
        'teknisi' => 'nullable|string|max:255',
        'foto_bukti' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
    ]);

    if ($request->hasFile('foto_bukti')) {
        $validated['foto_bukti'] = $request->file('foto_bukti')->store('foto_pemeliharaan', 'public');
    }

    HistoryPemeliharaan::create($validated);

    return redirect()->route('history-pemeliharaan.index')->with('success', 'Data histori berhasil ditambahkan!');
}


    public function edit($id)
    {
        $history = HistoryPemeliharaan::findOrFail($id);
        $mesins = Mesin::all();
        return view('pages.history.edit', compact('history', 'mesins'));
    }

    public function update(Request $request, $id)
    {
        $history = HistoryPemeliharaan::findOrFail($id);

        $validated = $request->validate([
            'mesin_id' => 'nullable|exists:mesin,id',
            'tanggal' => 'required|date',
            'jenis_pemeliharaan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi_jam' => 'nullable|numeric|min:0',
            'teknisi' => 'nullable|string|max:255',
            'verifikasi' => 'nullable|in:sudah,belum',
            'foto_bukti' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        if ($request->hasFile('foto_bukti')) {
            // Hapus foto lama jika ada
            if ($history->foto_bukti && Storage::disk('public')->exists($history->foto_bukti)) {
                Storage::disk('public')->delete($history->foto_bukti);
            }

            $validated['foto_bukti'] = $request->file('foto_bukti')->store('foto_pemeliharaan', 'public');
        }

        $history->update($validated);

        return redirect()->route('history-pemeliharaan.index')->with('success', 'Data histori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $history = HistoryPemeliharaan::findOrFail($id);

        if ($history->foto_bukti && Storage::disk('public')->exists($history->foto_bukti)) {
            Storage::disk('public')->delete($history->foto_bukti);
        }

        $history->delete();

        return redirect()->route('history-pemeliharaan.index')->with('success', 'Data histori berhasil dihapus!');
    }
}
