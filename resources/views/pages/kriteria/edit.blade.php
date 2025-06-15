@extends('layouts.app')

@section('title', 'Edit Kriteria')
@section('page-title', 'Edit Kriteria')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Edit Kriteria</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- Cek apakah $kriteria memiliki ID sebelum digunakan --}}
            @if(isset($kriteria->id))
                <form action="{{ route('kriteria.update', ['kriteria' => $kriteria->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nama_kriteria">Nama Kriteria</label>
                        <input type="text" name="nama_kriteria" id="nama_kriteria" class="form-control"
                               value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="bobot">Bobot Kriteria (0 - 1)</label>
                        <input type="number" name="bobot" id="bobot" class="form-control"
                               value="{{ old('bobot', $kriteria->bobot) }}" step="0.01" min="0" max="1" required>
                    </div>

                    <div class="form-group">
                        <label for="jenis_kriteria">Jenis Kriteria</label>
                        <select name="jenis_kriteria" id="jenis_kriteria" class="form-control" required>
                            <option value="benefit" {{ old('jenis_kriteria', $kriteria->jenis_kriteria) == 'benefit' ? 'selected' : '' }}>Benefit</option>
                            <option value="cost" {{ old('jenis_kriteria', $kriteria->jenis_kriteria) == 'cost' ? 'selected' : '' }}>Cost</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            @else
                <div class="alert alert-danger">Data Kriteria tidak ditemukan.</div>
            @endif
        </div>
    </div>
</div>
@endsection
