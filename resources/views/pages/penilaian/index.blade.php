@extends('layouts.app')

@section('title', 'Penilaian Mesin')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Data Penilaian Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('prioritas.index') }}">Prioritas Pemeliharaan</a></li>
        <li class="breadcrumb-item active"> Tabel Skor Mesin</li>
    </ol>


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

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Penilaian Mesin</div>
        <div>
            @if(auth()->user()->role === 'regu_mekanik')
            <form action="{{ route('penilaian.generate') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm"
            onclick="return confirm('Yakin ingin generate ulang data penilaian mesin?\nData lama akan diperbarui.')">
            <i class="fas fa-sync-alt me-1"></i> Generate Penilaian
        </button>
    </form>
            @endif
            <a href="{{ route('penilaian.normalisasi') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa-table me-1"></i> Lihat Hasil Normalisasi (SAW)
    </a>
        </div>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Tabel Penilaian --}}
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-bordered text-center align-middle" style="width:100%">
                    <thead class="thead-dark text-nowrap">
                        <tr>
                            <th>Nama Mesin</th>
                            <th>Akumulasi Penyusutan</th>
                            <th>Usia Mesin</th>
                            <th>Frekuensi Kerusakan</th>
                            <th>Waktu Downtime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penilaian as $p)
                            <tr class="text-center align-middle">
                                <td>{{ $p->mesin->nama_mesin ?? '-' }}</td>
                                <td>Rp{{ number_format($p->akumulasi_penyusutan, 0, ',', '.') }}</td>
                                <td>{{ $p->usia_mesin }} tahun</td>
                                <td>{{ round($p->frekuensi_kerusakan) }} kali</td>
                                <td>{{ $p->waktu_downtime }} jam</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data penilaian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
