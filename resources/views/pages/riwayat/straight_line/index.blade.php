@extends('layouts.app')

@section('title', 'Riwayat Perhitungan Straight Line')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Riwayat Perhitungan Penyusutan Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('depresiasi.index') }}">Perhitungan Penyusutan</a></li>
        <li class="breadcrumb-item active">Riwayat Perhitungan Straight Line</li>
    </ol>


    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Riwayat Perhitungan Straight Line</div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if($riwayats->isEmpty())
                <div class="alert alert-warning">Belum ada data riwayat perhitungan Straight Line.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th>Kode Perhitungan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($riwayats as $r)
                                <tr>
                                    <td>{{ $r->kode_perhitungan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('riwayat.straight-line.detail', $r->kode_perhitungan) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <form action="{{ route('riwayat.straight-line.destroy', $r->kode_perhitungan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
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
