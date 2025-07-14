@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Perhitungan SAW - {{ $mesin->nama_mesin }}</h2>

    <!-- Penjelasan Metode SAW -->
    <div class="alert alert-info">
        <h4>Cara Perhitungan SAW</h4>
        <p>SAW menggunakan metode normalisasi dan pembobotan sebagai berikut:</p>
        <ul>
            <li><strong>Normalisasi</strong> untuk <b>cost criteria</b>:<br>$$ r_{ij} = \frac{\min x_{j}}{x_{ij}} $$</li>
            <li><strong>Perhitungan skor akhir</strong>:<br>$$ V_i = \sum (r_{ij} \times w_j) $$</li>
        </ul>
    </div>

    <!-- Tabel Perhitungan -->
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Kriteria</th>
                <th>Nilai Awal (Xij)</th>
                <th>Normalisasi (Rij)</th>
                <th>Bobot (Wj)</th>
                <th>Hasil (Rij × Wj)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bobot = [
                    'akumulasi_penyusutan' => 0.3,
                    'usia_mesin' => 0.3,
                    'frekuensi_kerusakan' => 0.2,
                    'waktu_downtime' => 0.2,
                ];

                $labels = [
                    'akumulasi_penyusutan' => 'Akumulasi Penyusutan',
                    'usia_mesin' => 'Usia Mesin',
                    'frekuensi_kerusakan' => 'Frekuensi Kerusakan',
                    'waktu_downtime' => 'Waktu Downtime',
                ];
            @endphp

            @foreach ($data as $key => $nilai)
                <tr>
                    <td>{{ $labels[$key] }}</td>
                    <td>{{ number_format($nilai, 2) }}</td>
                    <td>{{ number_format($normalisasi[$key] ?? 0, 4) }}</td>
                    <td>{{ number_format($bobot[$key], 2) }}</td>
                    <td>{{ number_format(($normalisasi[$key] ?? 0) * $bobot[$key], 4) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total Skor Akhir (Vi)</th>
                <th>{{ number_format($skor_akhir, 4) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Tombol Navigasi -->
    <div class="mt-3">
        <a href="{{ route('prioritas.index') }}" class="btn btn-primary mb-3">
            <i class="fas fa-arrow-left"></i> Kembali</a>
        <a href="{{ route('prioritas.exportDetail', $mesin->id) }}" class="btn btn-success mb-3">
            <i class="fas fa-file-excel"></i> Export Excel</a>
        <a href="{{ route('prioritas.detailPDF', $mesin->id) }}" class="btn btn-danger mb-3" target="_blank">
            <i class="fas fa-file-pdf"></i> Print PDF</a>
    </div>
</div>

<!-- MathJax Render -->
<script type="text/javascript">
    MathJax = { tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] } };
</script>
<script async src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

@endsection
