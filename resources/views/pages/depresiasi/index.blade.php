@extends('layouts.app')

@section('title', 'Perhitungan Depresiasi')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Perhitungan Depresiasi (Straight Line)</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Hasil Perhitungan Penyusutan Mesin</li>
    </ol>

    <div class="card-body">
            <div class="alert alert-info d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
            <div>
                Rumus Depresiasi (Straight Line):<br>
                Depresiasi Tahunan = (Harga Pembelian - Nilai Sisa) / Umur Ekonomis
            </div>
        </div>
    </div>
</div>

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Data Perhitungan Penyusutan Mesin</div>
        </div>

        <div class="card-body">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('depresiasi.reset') }}" class="btn btn-primary mb-3" onclick="return confirm('Yakin ingin mereset semua data depresiasi?')">
                    <i class="fas fa-sync-alt"></i> Hitung Ulang
                </a>

                {{-- Tombol Export Excel --}}
                <a href="{{ route('depresiasi.exportExcel') }}" class="btn btn-outline-success mb-3">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            @endif

            {{-- Tombol Export PDF (sementara nonaktif atau target _blank) --}}
            <a href="{{ route('depresiasi.exportPdf') }}" class="btn btn-outline-danger mb-3" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-bordered text-center align-middle" style="width:100%">
                    <thead class="thead-dark text-nowrap">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Kode Mesin</th>
                            <th>Harga Beli</th>
                            <th>Nilai Sisa</th>
                            <th>Umur Ekonomis</th>
                            <th>Depresiasi Tahunan</th>
                            <th>Akumulasi Penyusutan ({{ now()->year }})</th>
                            <th>Nilai Buku ({{ now()->year }})</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mesins as $index => $m)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $m->nama_mesin }}</td>
                                <td>{{ $m->kode_mesin }}</td>
                                <td>Rp. {{ number_format($m->harga_beli, 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($m->nilai_sisa, 0, ',', '.') }}</td>
                                <td>{{ $m->umur_ekonomis }} tahun</td>
                                <td>
                                    Rp. {{ number_format($m->depresiasi_tahunan ?? 0, 0, ',', '.') }}
                                </td>
                                <td>Rp{{ number_format($m->total_akumulasi, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($m->nilai_buku_akhir, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('depresiasi.show', $m->id) }}" class="btn btn-info btn-sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
