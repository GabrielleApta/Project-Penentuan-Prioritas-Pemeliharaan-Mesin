@extends('layouts.app')

@section('title', 'Detail Perhitungan Straight Line')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Detail Perhitungan Straight Line - {{ $kode }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('riwayat.straight-line.index') }}">Riwayat Perhitungan</a></li>
        <li class="breadcrumb-item active">Detail Perhitungan</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <div class="alert alert-info d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
                <div>
                    <strong>Informasi:</strong><br>
                    Tabel berikut menampilkan hasil perhitungan penyusutan aset mesin berdasarkan metode <strong>Straight Line</strong> untuk kode perhitungan: <code>{{ $kode }}</code>.
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Detail Perhitungan Straight Line</div>
            <a href="{{ route('riwayat.straight-line.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card-body">
            @if($riwayat->isEmpty())
                <div class="alert alert-warning">Data detail tidak ditemukan.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th class="text-center">Nama Mesin</th>
                                <th>Tahun</th>
                                <th>Harga Beli</th>
                                <th>Nilai Sisa</th>
                                <th>Umur Ekonomis</th>
                                <th>Usia Mesin</th>
                                <th>Penyusutan/Tahun</th>
                                <th>Akumulasi</th>
                                <th>Nilai Buku</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($riwayat as $item)
                            <tr>
                                <td class="text-center">{{ $item->nama_mesin }}</td>
                                <td>{{ $item->tahun_pembelian }}</td>
                                <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->nilai_sisa, 0, ',', '.') }}</td>
                                <td>{{ $item->umur_ekonomis }} tahun</td>
                                <td>{{ $item->usia_mesin }} tahun</td>
                                <td>Rp {{ number_format($item->penyusutan_per_tahun, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->akumulasi_penyusutan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->nilai_buku, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
