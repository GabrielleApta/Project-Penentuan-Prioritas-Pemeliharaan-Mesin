@extends('layouts.app')

@section('title', 'Edit Data Kerusakan Tahunan')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Data Kerusakan Tahunan</h1>

    <form action="{{ route('kerusakan-tahunan.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="mesin_id" class="form-label">Nama Mesin</label>
            <select name="mesin_id" id="mesin_id" class="form-control" required>
                @foreach($mesin as $m)
                    <option value="{{ $m->id }}" {{ $data->mesin_id == $m->id ? 'selected' : '' }}>
                        {{ $m->nama_mesin }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" name="tahun" id="tahun" class="form-control" value="{{ $data->tahun }}" required min="2000" max="{{ date('Y') }}">
        </div>

        <div class="mb-3">
            <label for="kerusakan_ringan" class="form-label">Kerusakan Ringan</label>
            <input type="number" name="kerusakan_ringan" class="form-control" value="{{ $data->kerusakan_ringan }}" required min="0">
        </div>

        <div class="mb-3">
            <label for="downtime_ringan" class="form-label">Downtime Ringan (jam)</label>
            <input type="number" step="0.01" name="downtime_ringan" class="form-control" value="{{ $data->downtime_ringan }}" required min="0">
        </div>

        <div class="mb-3">
            <label for="kerusakan_parah" class="form-label">Kerusakan Parah</label>
            <input type="number" name="kerusakan_parah" class="form-control" value="{{ $data->kerusakan_parah }}" required min="0">
        </div>

        <div class="mb-3">
            <label for="downtime_parah" class="form-label">Downtime Parah (jam)</label>
            <input type="number" step="0.01" name="downtime_parah" class="form-control" value="{{ $data->downtime_parah }}" required min="0">
        </div>

        <div class="alert alert-info">
            <strong>Catatan:</strong> Skor frekuensi dan downtime akan diperbarui otomatis setelah menyimpan perubahan.
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('kerusakan-tahunan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
