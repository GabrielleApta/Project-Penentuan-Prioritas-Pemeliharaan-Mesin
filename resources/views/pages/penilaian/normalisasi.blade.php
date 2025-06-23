@extends('layouts.app')

@section('title', 'Normalisasi & Skor Akhir (SAW)')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Hasil Normalisasi & Skor Akhir (Metode SAW)</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Normalisasi dan Skor Akhir</li>
    </ol>

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Normalisasi Kriteria & Skor Akhir</div>
        </div>

        {{-- Tabel Normalisasi --}}
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-bordered text-center align-middle" style="width:100%">
                    <thead class="thead-dark text-nowrap">
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
                <a href="{{ route('penilaian.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Penilaian
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
