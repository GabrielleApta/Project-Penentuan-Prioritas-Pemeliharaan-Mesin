@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Penilaian Mesin</h2>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form Penilaian Mesin</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('penilaian.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="mesin_id" class="form-label">Pilih Mesin</label>
                    <select name="mesin_id" id="mesin_id" class="form-control" required>
                        <option value="">-- Pilih Mesin --</option>
                        @foreach ($mesin as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mesin }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tahun_penilaian" class="form-label">Tahun Penilaian</label>
                    <input type="number" name="tahun_penilaian" class="form-control" value="{{ date('Y') }}" required>
                </div>

                <div class="mb-3">
                    <label>Akumulasi Penyusutan</label>
                    <input type="number" step="0.01" name="akumulasi_penyusutan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Usia Mesin (tahun)</label>
                    <input type="number" name="usia_mesin" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Frekuensi Kerusakan (skor)</label>
                    <input type="number" step="0.01" name="frekuensi_kerusakan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Waktu Downtime (jam)</label>
                    <input type="number" step="0.01" name="waktu_downtime" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Simpan Penilaian</button>
            </form>
        </div>
    </div>
</div>
@endsection
