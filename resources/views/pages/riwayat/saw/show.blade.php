@extends('layouts.app')

@section('title', 'Detail Riwayat SAW')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Detail Riwayat SAW</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('riwayat-saw.index') }}">Riwayat SAW</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <strong>Kode Perhitungan:</strong> {{ $kode }} <br>
            <strong>Tanggal Simpan:</strong> {{ $items->first()->created_at->format('d M Y') }}
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Hasil Perhitungan</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Mesin</th>
                        <th>Akumulasi Penyusutan</th>
                        <th>Usia Mesin</th>
                        <th>Frekuensi Kerusakan</th>
                        <th>Waktu Downtime</th>

                        <th>Skor Akhir</th>
                        <th>Ranking</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start">{{ $item->nama_mesin }}</td>
                            <td>{{ $item->akumulasi_penyusutan == floor($item->akumulasi_penyusutan) ? number_format($item->akumulasi_penyusutan, 0) : $item->akumulasi_penyusutan }}</td>
                            <td>{{ $item->usia_mesin == floor($item->usia_mesin) ? number_format($item->usia_mesin, 0) : $item->usia_mesin }}</td>
                            <td>{{ $item->frekuensi_kerusakan == floor($item->frekuensi_kerusakan) ? number_format($item->frekuensi_kerusakan, 0) : $item->frekuensi_kerusakan }}</td>
                            <td>{{ $item->waktu_downtime == floor($item->waktu_downtime) ? number_format($item->waktu_downtime, 0) : $item->waktu_downtime }}</td>

                            <td>{{ $item->skor_akhir == floor($item->skor_akhir) ? number_format($item->skor_akhir, 0) : $item->skor_akhir }}</td>
                            <td><strong>{{ $item->ranking }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
