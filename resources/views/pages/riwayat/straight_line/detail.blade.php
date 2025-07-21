@extends('layouts.app')

@section('title', 'Detail Perhitungan Straight Line')

@section('content')
<div class="container mt-4">
    <h2>Detail Perhitungan - {{ $kode }}</h2>

    <a href="{{ route('riwayat.straight-line.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mesin</th>
                <th>Tahun</th>
                <th>Harga Beli</th>
                <th>Nilai Sisa</th>
                <th>Umur Ekonomis</th>
                <th>Usia Mesin</th>
                <th>Penyusutan/Tahun</th>
                <th>Akumulasi</th>
                <th>Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $item)
                <tr>
                    <td>{{ $item->nama_mesin }}</td>
                    <td>{{ $item->tahun_pembelian }}</td>
                    <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->nilai_sisa, 0, ',', '.') }}</td>
                    <td>{{ $item->umur_ekonomis }} tahun</td>
                    <td>{{ $item->usia_mesin }} tahun</td>
                    <td>Rp {{ number_format($item->penyusutan_per_tahun, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->akumulasi_penyusutan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->nilai_buku, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
