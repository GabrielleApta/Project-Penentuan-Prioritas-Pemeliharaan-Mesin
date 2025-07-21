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
                <th class="text-center">Kode Perhitungan</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayats as $r)
                <tr>
                    <td class="text-center">{{ $r->kode_perhitungan }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('riwayat.straight-line.detail', $r->kode_perhitungan) }}" class="btn btn-info btn-sm">Detail</a>
                        <form action="{{ route('riwayat.straight-line.destroy', $r->kode_perhitungan) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
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
