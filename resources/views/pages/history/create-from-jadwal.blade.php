@extends('layouts.app')

@section('title', 'Input Histori dari Jadwal')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Input Histori Pemeliharaan</h1>

    <form action="{{ route('histori.store.from.jadwal') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
        <input type="hidden" name="mesin_id" value="{{ $jadwal->mesin->id }}">

        <div class="mb-3">
            <label>Nama Mesin</label>
            <input type="text" class="form-control" value="{{ $jadwal->mesin->nama_mesin }}" disabled>
        </div>

        <div class="mb-3">
            <label>Tanggal Pemeliharaan</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $jadwal->tanggal }}" required>
        </div>

        <div class="mb-3">
            <label>Jenis Pemeliharaan</label>
            <input type="text" name="jenis_pemeliharaan" class="form-control" value="{{ $jadwal->jenis_pemeliharaan }}" readonly>
        </div>

        <div class="mb-3">
            <label>Teknisi Pelaksana</label>
            <input type="text" name="teknisi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Deskripsi Pekerjaan</label>
            <textarea name="deskripsi" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Durasi (Jam)</label>
            <input type="number" step="0.1" name="durasi_jam" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Upload Foto Bukti</label>
            <input type="file" name="foto_bukti" class="form-control" accept="image/*">
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
