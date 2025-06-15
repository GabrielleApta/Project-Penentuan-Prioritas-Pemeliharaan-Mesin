@extends('layouts.app')

@section('title', 'Perhitungan SAW')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Hasil Perhitungan SAW</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{ route ('dashboard.index') }}"> Dashboard</a> / Hasil Perhitungan SAW</h6>
        </div>
    <div class="card-body">
    @if(auth()->user()->role === 'admin')
    <a href="{{ route('prioritas.hitung') }}" class="btn btn-primary mb-3">
        <i class="fas fa-calculator"></i> Hitung Ulang SAW</a>
    <a href="{{ route('prioritas.export') }}" class="btn btn-success mb-3">
        <i class="fas fa-file-excel"></i> Export Excel</a>
    @endif
    <a href="{{ route('prioritas.printPDF') }}" class="btn btn-danger mb-3" target="_blank">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($hasil_saw->isEmpty())
        <div class="alert alert-warning">Belum ada hasil perhitungan SAW.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th>Rangking</th>
                    <th>Nama Mesin</th>
                    <th>Skor Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hasil_saw as $hasil)
                <tr class="text-center">
                    <td>{{ $hasil->rangking }}</td>
                    <td>{{ $hasil->mesin->nama_mesin }}</td>
                    <td>{{ fmod($hasil->skor_akhir, 1) == 0 ? intval($hasil->skor_akhir) : number_format($hasil->skor_akhir, 4) }}</td>
                    <td>
                        <a href="{{ route('prioritas.detail', $hasil->mesin_id) }}" class="btn btn-info">
                            <i class="fa-solid fa-circle-exclamation"></i> Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
