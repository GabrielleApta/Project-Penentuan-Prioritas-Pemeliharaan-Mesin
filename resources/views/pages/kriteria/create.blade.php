@extends('layouts.app')

@section('title', 'Tambah Kriteria')
@section('page-title', 'Tambah Kriteria')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Tambah Kriteria</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- Menampilkan error jika ada --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('kriteria.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nama_kriteria">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" id="nama_kriteria" class="form-control" required value="{{ old('nama_kriteria') }}">
                </div>
                <div class="form-group">
                    <label for="bobot">Bobot Kriteria</label>
                    <input type="number" name="bobot" id="bobot" class="form-control" step="0.01" required value="{{ old('bobot') }}">
                </div>
                <div class="form-group">
                    <label for="jenis_kriteria">Jenis Kriteria</label>
                    <select name="jenis_kriteria" id="jenis_kriteria" class="form-control" required>
                        <option value="benefit" {{ old('jenis_kriteria') == 'benefit' ? 'selected' : '' }}>Benefit</option>
                        <option value="cost" {{ old('jenis_kriteria') == 'cost' ? 'selected' : '' }}>Cost</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
