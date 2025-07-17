@extends('layouts.app')

@section('title', 'Perhitungan SAW')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Ranking Prioritas Pemeliharaan Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Hasil Perhitungan SAW</li>
    </ol>

    <div class="card mb-4">
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-start">
            <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
            <div>
                Data mesin di bawah ini menjadi dasar dalam proses perhitungan prioritas pemeliharaan berbasis metode <em><strong> Straight Line</strong></em> dan <strong>SAW</strong>.<br>
                Pastikan seluruh informasi mesin seperti harga beli, tahun pembelian sudah terisi dengan benar.
            </div>
        </div>
    </div>
</div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Data Ranking Prioritas Pemeliharaan Mesin</div>
            <div>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('prioritas.hitung') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-calculator"></i> Hitung Ulang
                    </a>
                    <a href="{{ route('penilaian.index') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-table me-1"></i> Penilaian Mesin
                    </a>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-table me-1"></i> Jadwal Pemeliharaan
                    </a>
                @endif
                <a href="{{ route('prioritas.printPDF') }}" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf"></i> Riwayat Perhitungan
                </a>
        </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
            @endif

            @if($hasil_saw->isEmpty())
                <div class="alert alert-warning">Belum ada hasil perhitungan SAW.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th>Rangking</th>
                                <th>Nama Mesin</th>
                                <th>Skor Akhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($hasil_saw as $hasil)
                                <tr>
                                    <td>{{ $hasil->rangking }}</td>
                                    <td>{{ optional($hasil->mesin)->nama_mesin ?? '-' }}</td>
                                    <td>{{ number_format($hasil->skor_akhir, 4) }}</td>
                                    <td>
                                        <a href="{{ route('prioritas.detail', $hasil->mesin_id) }}" class="btn btn-info">
                                            <i class="fa-solid fa-circle-exclamation"></i> Detail
                                        </a>
                                    </td>
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
