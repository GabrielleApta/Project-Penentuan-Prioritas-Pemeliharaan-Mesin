@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Penilaian Mesin</h2>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form Edit Penilaian</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('penilaian.update', $penilaian->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Nama Mesin</label>
                    <input type="text" class="form-control" value="{{ $penilaian->mesin->nama_mesin }}" disabled>
                </div>

                <div class="mb-3">
                    <label>Tahun Penilaian</label>
                    <input type="number" name="tahun_penilaian" class="form-control" value="{{ $penilaian->tahun_penilaian }}" required>
                </div>

                <div class="mb-3">
                    <label>Akumulasi Penyusutan</label>
                    <input type="number" step="0.01" name="akumulasi_penyusutan" class="form-control" value="{{ $penilaian->akumulasi_penyusutan }}" required>
                </div>

                <div class="mb-3">
                    <label>Usia Mesin (tahun)</label>
                    <input type="number" name="usia_mesin" class="form-control" value="{{ $penilaian->usia_mesin }}" required>
                </div>

                <div class="mb-3">
                    <label>Frekuensi Kerusakan</label>
                    <input type="number" step="0.01" name="frekuensi_kerusakan" class="form-control" value="{{ $penilaian->frekuensi_kerusakan }}" required>
                </div>

                <div class="mb-3">
                    <label>Waktu Downtime</label>
                    <input type="number" step="0.01" name="waktu_downtime" class="form-control" value="{{ $penilaian->waktu_downtime }}" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Update Penilaian</button>
            </form>
        </div>
    </div>
</div>
@endsection
