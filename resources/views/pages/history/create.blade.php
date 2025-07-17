@extends('layouts.app')

@section('title', 'Tambah Histori Pemeliharaan')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Histori Pemeliharaan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('history-pemeliharaan.index') }}">Histori Pemeliharaan</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('history-pemeliharaan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="mesin_id" class="form-label">Nama Mesin</label>
                    <select name="mesin_id" class="form-control @error('mesin_id') is-invalid @enderror">
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($mesins as $m)
                            <option value="{{ $m->id }}" {{ old('mesin_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mesin }}
                            </option>
                        @endforeach
                    </select>
                    @error('mesin_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Pemeliharaan</label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}">
                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="jenis_pemeliharaan" class="form-label">Jenis Pemeliharaan</label>
                    <select name="jenis_pemeliharaan" class="form-control @error('jenis_pemeliharaan') is-invalid @enderror">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="preventive" {{ old('jenis_pemeliharaan') == 'preventive' ? 'selected' : '' }}>Preventif</option>
<option value="corrective" {{ old('jenis_pemeliharaan') == 'corrective' ? 'selected' : '' }}>Korektif</option>

                    </select>
                    @error('jenis_pemeliharaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="durasi_jam" class="form-label">Durasi Pemeliharaan (jam)</label>
                    <input type="number" name="durasi_jam" class="form-control @error('durasi_jam') is-invalid @enderror" value="{{ old('durasi_jam') }}">
                    @error('durasi_jam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="teknisi" class="form-label">Nama Teknisi</label>
                    <input type="text" name="teknisi" class="form-control @error('teknisi') is-invalid @enderror" value="{{ old('teknisi') }}">
                    @error('teknisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="foto_bukti" class="form-label">Upload Foto Bukti</label>
                    <input type="file" name="foto_bukti" class="form-control @error('foto_bukti') is-invalid @enderror">
                    @error('foto_bukti') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>

                <a href="{{ route('history-pemeliharaan.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
