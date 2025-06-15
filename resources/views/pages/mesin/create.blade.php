@extends('layouts.app')

@section('title', 'Tambah Data')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Mesin</h2>
    <div class="card p-4 shadow">
        <form action="{{ route('mesin.store') }}" method="POST">
            @csrf
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="nama_mesin" name="nama_mesin" placeholder="Nama Mesin" required>
                <label for="nama_mesin">Nama Mesin</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="kode_mesin" name="kode_mesin" placeholder="Kode Mesin" required>
                <label for="kode_mesin">Kode Mesin</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" placeholder="Harga Beli" required>
                <label for="harga_beli">Harga Beli</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="tahun_pembelian" name="tahun_pembelian" placeholder="Tahun Pembelian" required>
                <label for="tahun_pembelian">Tahun Pembelian</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="spesifikasi_mesin" name="spesifikasi_mesin" placeholder="Spesifikasi Mesin" required>
                <label for="spesifikasi_mesin">Spesifikasi Mesin</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" class="form-control" step="0.01" id="daya_motor" name="daya_motor" placeholder="Daya Motor" required>
                <label for="daya_motor">Daya Motor</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="lokasi_mesin" name="lokasi_mesin" placeholder="Lokasi Mesin" required>
                <label for="lokasi_mesin">Lokasi Mesin</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="nilai_sisa" name="nilai_sisa" placeholder="Nilai Sisa" required>
                <label for="nilai_sisa">Nilai Sisa</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="umur_ekonomis" name="umur_ekonomis" placeholder="Umur Ekonomis" required>
                <label for="umur_ekonomis">Umur Ekonomis (tahun)</label>
            </div>
            <div class="form-floating mb-3">
                <select class="form-control" id="status" name="status" required>
                    <option value="aktif" selected>Aktif</option>
                    <option value="tidak aktif">Tidak Aktif</option>
                </select>
                <label for="status">Status Mesin</label>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('mesin.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
