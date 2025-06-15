@extends('layouts.app')

@section('title', 'Kriteria dan Bobot')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Kriteria dan Bobot</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{ route ('dashboard.index') }}"> Dashboard</a> / Data Kriteria dan Bobot</h6>
        </div>
        <div class="card-body">
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('kriteria.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Tambah Kriteria
            </a>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Kriteria</th>
                            <th>Bobot</th>
                            <th>Jenis Kriteria</th>
                            @if(auth()->user()->role === 'admin')
                            <th>Aksi</th>
                            @endif

                        </tr>
                    </thead>
<tbody>
    @foreach ($kriteria as $index => $item)
    <tr class="text-center">
        <td>{{ $index + 1 }}</td>
        <td>{{ $item->nama_kriteria }}</td>
        <td>{{ $item->bobot * 100 }}%</td>
        <td>{{ $item->jenis_kriteria }}</td>
        @if(auth()->user()->role === 'admin')
        <td>
                <a href="{{ route('kriteria.edit', $item->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('kriteria.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
        </td>
        @endif
    </tr>
    @endforeach
</tbody>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection
