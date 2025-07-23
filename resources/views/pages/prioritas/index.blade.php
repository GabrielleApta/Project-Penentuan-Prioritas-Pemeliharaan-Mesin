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
        <strong>Informasi:</strong><br>
        Perhitungan prioritas pemeliharaan menggunakan dua metode yaitu <em>Straight Line</em> (untuk depresiasi aset) dan <strong>SAW</strong> (untuk penilaian multi-kriteria).<br>
        Data di bawah merupakan hasil akhir ranking berdasarkan metode <strong>SAW</strong>.<br>
        <ul class="mb-0 mt-1">
            <li>Pastikan data mesin seperti <strong>harga beli</strong>, <strong>nilai sisa</strong>, <strong>umur ekonomis</strong>, dan <strong>tahun pembelian</strong> sudah diisi dengan benar.</li>
            <li>Gunakan tombol <strong>Hitung Ulang</strong> jika ada perubahan data mesin.</li>
            <li>Gunakan tombol <strong>Simpan ke Riwayat</strong> untuk menyimpan hasil saat ini sebagai dokumentasi tetap.</li>
        </ul>
    </div>
</div>

    </div>
</div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
    <div><i class="fas fa-table me-1"></i> Tabel Data Ranking Prioritas Pemeliharaan Mesin</div>
    <div class="d-flex flex-wrap gap-2 justify-content-end">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('prioritas.hitung') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-calculator"></i> Hitung Ulang
            </a>

            <a href="{{ route('penilaian.index') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-table me-1"></i> Penilaian Mesin
            </a>

            <form action="{{ route('riwayat-saw.store') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm"
                    onclick="return confirm('Simpan perhitungan ini ke riwayat?')">
                    <i class="fas fa-save"></i> Simpan ke Riwayat
                </button>
            </form>
        @endif

        <a href="{{ route('riwayat-saw.index') }}" class="btn btn-outline-danger btn-sm">
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
