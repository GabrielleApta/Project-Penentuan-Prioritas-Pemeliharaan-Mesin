<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesin;
use App\Models\Prioritas;
use App\Models\PenilaianMesin;
use Illuminate\Support\Facades\DB;
use PDF;

class PrioritasController extends Controller
{
    protected $tahun = 2024;

    protected $kriteria = [
        'akumulasi_penyusutan' => ['bobot' => 0.3, 'jenis' => 'cost'],
        'usia_mesin'           => ['bobot' => 0.3, 'jenis' => 'cost'],
        'frekuensi_kerusakan'  => ['bobot' => 0.2, 'jenis' => 'cost'],
        'waktu_downtime'       => ['bobot' => 0.2, 'jenis' => 'cost'],
    ];

    public function __construct()
    {
        $this->middleware('user.readonly')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $hasil_saw = Prioritas::with('mesin')->orderBy('rangking')->get();
        return view('pages.prioritas.index', compact('hasil_saw'));
    }

    public function hitungSAW()
    {
        $penilaian = PenilaianMesin::with('mesin')
            ->where('tahun_penilaian', $this->tahun)
            ->get();

        if ($penilaian->isEmpty()) {
            return back()->with('error', "Data penilaian tahun {$this->tahun} belum tersedia.");
        }

        // Ambil nilai mentah
        $data_nilai = $penilaian->map(fn($item) => [
            'mesin_id'             => $item->mesin_id,
            'akumulasi_penyusutan' => $item->akumulasi_penyusutan,
            'usia_mesin'           => $item->usia_mesin,
            'frekuensi_kerusakan'  => $item->frekuensi_kerusakan,
            'waktu_downtime'       => $item->waktu_downtime,
        ])->toArray();

        // Normalisasi dinamis
        $normalisasi = $this->normalisasi($data_nilai);

        // Hitung skor akhir
        $hasil = [];
        foreach ($normalisasi as $row) {
            $skor = 0;
            foreach ($this->kriteria as $key => $meta) {
                $skor += $row[$key] * $meta['bobot'];
            }
            $hasil[] = [
                'mesin_id'   => $row['mesin_id'],
                'skor_akhir' => round($skor, 4),
            ];
        }

        // Urutkan & ranking (semua cost → ASC)
        usort($hasil, fn($a, $b) => $a['skor_akhir'] <=> $b['skor_akhir']);
        foreach ($hasil as $i => &$row) {
            $row['rangking'] = $i + 1;
        }

        DB::table('hasil_saw')->truncate();
        Prioritas::insert($hasil);

        return redirect()->route('prioritas.index')->with('success', 'Perhitungan SAW berhasil dilakukan.');
    }

    public function detailSAW($mesin_id)
    {
        $penilaian = PenilaianMesin::with('mesin')
            ->where('mesin_id', $mesin_id)
            ->where('tahun_penilaian', $this->tahun)
            ->firstOrFail();

        $data = $penilaian->only(array_keys($this->kriteria));
        $data['mesin_id'] = $mesin_id;

        $semua = PenilaianMesin::where('tahun_penilaian', $this->tahun)->get()->map(fn($p) => [
            'mesin_id'             => $p->mesin_id,
            'akumulasi_penyusutan' => $p->akumulasi_penyusutan,
            'usia_mesin'           => $p->usia_mesin,
            'frekuensi_kerusakan'  => $p->frekuensi_kerusakan,
            'waktu_downtime'       => $p->waktu_downtime,
        ])->toArray();

        $normal = collect($this->normalisasi($semua))->firstWhere('mesin_id', $mesin_id);

        $skor_akhir = 0;
        foreach ($this->kriteria as $k => $meta) {
            $skor_akhir += ($normal[$k] ?? 0) * $meta['bobot'];
        }

        return view('pages.prioritas.detail', [
            'mesin'        => $penilaian->mesin,
            'data'         => $data,
            'normalisasi'  => $normal,
            'skor_akhir'   => round($skor_akhir, 4),
        ]);
    }

    public function printPDF()
    {
        $hasil_saw = Prioritas::with('mesin')->orderBy('rangking')->get();
        $pdf = PDF::loadView('pages.prioritas.printPDF', compact('hasil_saw'));
        return $pdf->stream('hasil_saw.pdf');
    }

    public function detailPDF($mesin_id)
    {
        return $this->cetakDetail($mesin_id, true);
    }

    private function cetakDetail($mesin_id, $pdf = false)
    {
        $penilaian = PenilaianMesin::with('mesin')
            ->where('mesin_id', $mesin_id)
            ->where('tahun_penilaian', $this->tahun)
            ->firstOrFail();

        $data = $penilaian->only(array_keys($this->kriteria));
        $data['mesin_id'] = $mesin_id;

        $semua = PenilaianMesin::where('tahun_penilaian', $this->tahun)->get()->map(fn($p) => [
            'mesin_id'             => $p->mesin_id,
            'akumulasi_penyusutan' => $p->akumulasi_penyusutan,
            'usia_mesin'           => $p->usia_mesin,
            'frekuensi_kerusakan'  => $p->frekuensi_kerusakan,
            'waktu_downtime'       => $p->waktu_downtime,
        ])->toArray();

        $normal = collect($this->normalisasi($semua))->firstWhere('mesin_id', $mesin_id);

        $skor_akhir = 0;
        foreach ($this->kriteria as $k => $meta) {
            $skor_akhir += ($normal[$k] ?? 0) * $meta['bobot'];
        }

        $viewData = [
            'mesin'       => $penilaian->mesin,
            'data'        => $data,
            'normalisasi' => $normal,
            'skor_akhir'  => round($skor_akhir, 4),
            'pdf'         => $pdf,
        ];

        return $pdf
            ? PDF::loadView('pages.prioritas.detailPDF', $viewData)->stream("detail_saw_{$mesin_id}.pdf")
            : view('pages.prioritas.detail', $viewData);
    }

    private function normalisasi(array $data)
    {
        $result = [];

        foreach ($this->kriteria as $k => $meta) {
            $values = array_column($data, $k);
            $extreme = $meta['jenis'] === 'benefit' ? max($values) : min($values);
            $extreme = $extreme ?: 0.0001;

            foreach ($data as $i => $row) {
                $val = $row[$k] ?: 0.0001;
                $norm = $meta['jenis'] === 'benefit' ? $val / $extreme : $extreme / $val;

                $result[$i]['mesin_id'] = $row['mesin_id'];
                $result[$i][$k] = $norm;
            }
        }

        return $result;
    }
}
