@extends('layouts.app')

@section('title', 'Tambah Jadwal Pemeliharaan')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Tambah Jadwal Pemeliharaan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal Pemeliharaan</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('jadwal.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="mesin_id" class="form-label">Pilih Mesin</label>
                    <select name="mesin_id" id="mesin_id" class="form-control" required>
                        <option value="">-- Pilih Mesin --</option>
                        @foreach ($mesins as $mesin)
                            <option value="{{ $mesin->id }}">{{ $mesin->nama_mesin }} ({{ $mesin->kode_mesin }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal_jadwal" class="form-label">Tanggal Jadwal</label>
                    <input type="date" name="tanggal_jadwal" id="tanggal_jadwal" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="prioritas" class="form-label">Prioritas</label>
                    <select name="prioritas" id="prioritas" class="form-control" required>
                        <option value="tinggi">Tinggi</option>
                        <option value="sedang">Sedang</option>
                        <option value="rendah">Rendah</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
