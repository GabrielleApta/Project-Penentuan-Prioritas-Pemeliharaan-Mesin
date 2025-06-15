@extends('layouts.app')

@section('title', 'Tambah Data Kerusakan Tahunan')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Data Kerusakan Tahunan</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kerusakan-tahunan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="mesin_id" class="form-label">Nama Mesin</label>
            <select name="mesin_id" id="mesin_id" class="form-control" required>
                <option value="">-- Pilih Mesin --</option>
                @foreach($mesin as $m)
                    <option value="{{ $m->id }}" {{ old('mesin_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->nama_mesin }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" name="tahun" id="tahun" class="form-control" value="{{ old('tahun') }}" required min="2000" max="{{ date('Y') }}">
        </div>

        <div class="mb-3">
            <label for="kerusakan_ringan" class="form-label">Jumlah Kerusakan Ringan</label>
            <input type="number" name="kerusakan_ringan" id="kerusakan_ringan" class="form-control" value="{{ old('kerusakan_ringan') }}" required min="0">
        </div>

        <div class="mb-3">
            <label for="downtime_ringan" class="form-label">Total Downtime Ringan (jam)</label>
            <input type="number" step="0.01" name="downtime_ringan" id="downtime_ringan" class="form-control" value="{{ old('downtime_ringan') }}" required min="0">
        </div>

        <div class="mb-3">
            <label for="kerusakan_parah" class="form-label">Jumlah Kerusakan Parah</label>
            <input type="number" name="kerusakan_parah" id="kerusakan_parah" class="form-control" value="{{ old('kerusakan_parah') }}" required min="0">
        </div>

        <div class="mb-3">
            <label for="downtime_parah" class="form-label">Total Downtime Parah (jam)</label>
            <input type="number" step="0.01" name="downtime_parah" id="downtime_parah" class="form-control" value="{{ old('downtime_parah') }}" required min="0">
        </div>

        <div class="alert alert-info">
            <strong>Catatan:</strong> Skor frekuensi dan downtime akan dihitung otomatis berdasarkan data kerusakan.
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('kerusakan-tahunan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
