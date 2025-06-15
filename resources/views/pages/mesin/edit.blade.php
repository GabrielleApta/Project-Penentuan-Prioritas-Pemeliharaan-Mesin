@extends('layouts.app')

@section('title', 'Edit Mesin')

@section('content')
    <h1 class="mt-4">Edit Mesin</h1>

    <form action="{{ route('mesin.update', $mesin->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nama Mesin</label>
            <input type="text" name="nama_mesin" class="form-control" value="{{ $mesin->nama_mesin }}" required>
        </div>
        <div class="mb-3">
            <label>Kode Mesin</label>
            <input type="text" name="kode_mesin" class="form-control" value="{{ $mesin->kode_mesin }}" required>
        </div>
        <div class="mb-3">
            <label>Harga Beli</label>
            <input type="number" name="harga_beli" class="form-control" value="{{ $mesin->harga_beli }}" required>
        </div>
        <div class="mb-3">
            <label>Tahun Pembelian</label>
            <input type="number" name="tahun_pembelian" class="form-control" value="{{ $mesin->tahun_pembelian }}" required>
        </div>
        <div class="mb-3">
            <label>Spesifikasi Mesin</label>
            <input type="text" name="spesifikasi_mesin" class="form-control" value="{{ $mesin->spesifikasi_mesin }}" required>
        </div>
        <div class="mb-3">
            <label>Daya Motor</label>
            <input type="number" name="daya_motor" class="form-control" value="{{ $mesin->daya_motor }}" required>
        </div>
        <div class="mb-3">
            <label>Lokasi Mesin</label>
            <input type="text" name="lokasi_mesin" class="form-control" value="{{ $mesin->lokasi_mesin }}" required>
        </div>
        <div class="mb-3">
            <label>Nilai Sisa</label>
            <input type="number" name="nilai_sisa" class="form-control" value="{{ $mesin->nilai_sisa }}" required>
        </div>
        <div class="mb-3">
            <label>Umur Ekonomis (tahun)</label>
            <input type="number" name="umur_ekonomis" class="form-control" value="{{ $mesin->umur_ekonomis }}" required>
        </div>
        <div class="mb-3">
            <label>Status Mesin</label>
            <select name="status" class="form-select" required>
                <option value="aktif" {{ $mesin->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak aktif" {{ $mesin->status == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-warning">Update</button>
        <a href="{{ route('mesin.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection
