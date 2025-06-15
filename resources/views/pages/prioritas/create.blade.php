@extends('layouts.app')

@section('title', 'Tambah Data Mesin')
@section('page-title', 'Tambah Data Mesin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Tambah Data Mesin</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('prioritas.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="mesin_id">Pilih Mesin</label>
                    <select name="mesin_id" id="mesin_id" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Mesin --</option>
                        @foreach ($mesin as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mesin }} - {{ $m->kode_mesin }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga_beli">Harga Beli</label>
                    <input type="number" name="harga_beli" id="harga_beli" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="nilai_sisa">Nilai Sisa</label>
                    <input type="number" name="nilai_sisa" id="nilai_sisa" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="umur_ekonomis">Umur Ekonomis (tahun)</label>
                    <input type="number" name="umur_ekonomis" id="umur_ekonomis" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('prioritas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
