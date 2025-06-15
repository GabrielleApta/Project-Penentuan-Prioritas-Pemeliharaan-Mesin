@extends('layouts.app')

@section('title', 'Normalisasi & Skor Akhir (SAW)')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Hasil Normalisasi & Skor Akhir (Metode SAW)</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Tabel Normalisasi Kriteria & Skor Akhir
            </h6>
        </div>

        <div class="card-body">
            {{-- Tabel Normalisasi --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nama Mesin</th>
                            <th>Norm. Akumulasi Penyusutan</th>
                            <th>Norm. Usia Mesin</th>
                            <th>Norm. Frek. Kerusakan</th>
                            <th>Norm. Downtime</th>
                            <th>Skor Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($normalisasi as $item)
                            <tr>
                                <td>{{ $item['mesin'] }}</td>
                                <td>
                                    {{ fmod($item['norm_penyusutan'], 1) == 0 ? number_format($item['norm_penyusutan'], 0, ',', '.') : number_format($item['norm_penyusutan'], 6, ',', '.') }}
                                </td>
                                <td>
                                    {{ fmod($item['norm_usia'], 1) == 0 ? number_format($item['norm_usia'], 0, ',', '.') : number_format($item['norm_usia'], 6, ',', '.') }}
                                </td>
                                <td>
                                    {{ fmod($item['norm_frekuensi'], 1) == 0 ? number_format($item['norm_frekuensi'], 0, ',', '.') : number_format($item['norm_frekuensi'], 6, ',', '.') }}
                                </td>
                                <td>
                                    {{ fmod($item['norm_downtime'], 1) == 0 ? number_format($item['norm_downtime'], 0, ',', '.') : number_format($item['norm_downtime'], 6, ',', '.') }}
                                </td>
                                <td><strong>
                                    {{ fmod($item['skor_akhir'], 1) == 0 ? number_format($item['skor_akhir'], 0, ',', '.') : number_format($item['skor_akhir'], 6, ',', '.') }}
                                </strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data normalisasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tombol Kembali --}}
            <div class="mt-4">
                <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">⬅️ Kembali ke Penilaian</a>
            </div>
        </div>
    </div>
</div>
@endsection
