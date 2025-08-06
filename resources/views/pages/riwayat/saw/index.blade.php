@extends('layouts.app')

@section('title', 'Riwayat Perhitungan SAW')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Riwayat Perhitungan Penentuan Prioritas Pemeliharaan Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Riwayat Perhitungan SAW</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <div class="alert alert-info d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
                <div>
                    <strong>Informasi:</strong><br>
                    Halaman ini menampilkan daftar riwayat perhitungan metode <strong>SAW</strong> untuk menentukan prioritas pemeliharaan mesin.<br>
                    Anda dapat melihat detail hasil perhitungan atau menghapus riwayat yang tidak lagi diperlukan.
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-history me-1"></i> Tabel Riwayat Perhitungan SAW</div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="{{ route('prioritas.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Ranking
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

            @if($riwayat->isEmpty())
                <div class="alert alert-warning">Belum ada data riwayat perhitungan SAW.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Kode Perhitungan</th>
                                <th>Tanggal Generate</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($riwayat->groupBy('kode_perhitungan') as $kode => $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $kode }}</td>
                                <td>{{ $group->first()->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('riwayat-saw.show', ['kode' => $kode]) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <form action="{{ route('riwayat-saw.destroy', ['kode' => $kode]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </form>
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
