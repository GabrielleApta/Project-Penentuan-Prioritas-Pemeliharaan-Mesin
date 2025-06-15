@extends('layouts.app')

@section('title', 'Penilaian Mesin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 text-gray-800">Data Penilaian Mesin</h1>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 text-primary fw-bold">
                <i class="fas fa-cogs me-1"></i>
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-primary">Dashboard</a> /
                Tabel Skor Mesin
            </h6>
        </div>

        <div class="card-body">
            {{-- Tombol Aksi untuk Admin --}}
            @if(auth()->user()->role === 'admin')
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <form action="{{ route('penilaian.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm"
                            onclick="return confirm('Generate penilaian mesin berdasarkan data 2022–2024?\nData lama akan diperbarui.')">
                            <i class="fas fa-sync-alt me-1"></i> Generate Penilaian
                        </button>
                    </form>

                    <a href="{{ route('penilaian.normalisasi') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-table me-1"></i> Lihat Hasil Normalisasi (SAW)
                    </a>

                    <a href="{{ route('penilaian.exportExcel') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export ke Excel
                    </a>
                </div>
            @endif

            {{-- Alert sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Tabel Penilaian --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable">
                    <thead class="table-dark text-nowrap text-center">
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
</div>
@endsection

