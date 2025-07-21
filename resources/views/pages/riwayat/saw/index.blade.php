@extends('layouts.app')

@section('title', 'Riwayat Perhitungan Straight Line')

@section('content')
<div class="container mt-4">
    <h2>Riwayat Perhitungan Straight Line</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Perhitungan</th>
            <th>Tanggal Generate</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($riwayat->groupBy('kode_perhitungan') as $kode => $group)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $kode }}</td>
            <td>{{ $group->first()->created_at->format('d M Y') }}</td>
            <td>
                <a href="{{ route('riwayat-saw.show', ['kode' => $group->first()->kode_perhitungan]) }}" class="btn btn-info btn-sm">Detail</a>
                <form action="{{ route('riwayat-saw.destroy', $group->first()->kode_perhitungan) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
