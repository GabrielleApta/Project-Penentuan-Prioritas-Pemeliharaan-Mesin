@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Detail Mesin: {{ $mesin->nama_mesin }}</h1>

    <!-- Informasi Mesin -->
    <div class="card mb-4">
        <div class="card-header">Informasi Mesin</div>
        <div class="card-body">
            <p><strong>Kode Mesin:</strong> {{ $mesin->kode_mesin }}</p>
            <p><strong>Nama Mesin:</strong> {{ $mesin->nama_mesin }}</p>
            <p><strong>Tahun Pembelian:</strong> {{ $mesin->tahun_pembelian }}</p>
            <p><strong>Harga Perolehan:</strong> Rp. {{ number_format($mesin->harga_beli, 0, ',', '.') }}</p>
            <p><strong>Umur Ekonomis:</strong> {{ $mesin->umur_ekonomis }} tahun</p>
        </div>
    </div>

    @php
        $tahun_akhir = $mesin->tahun_pembelian + $mesin->umur_ekonomis - 1;
    @endphp

    <!-- Tabel Depresiasi -->
    <div class="card">
        <div class="card-header">Detail Depresiasi per Tahun</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th>Tahun</th>
                            <th>Nilai Depresiasi</th>
                            <th>Akumulasi Penyusutan</th>
                            <th>Nilai Buku Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($depresiasi as $row)
                            @php
                                $isLuarUmur = $row->tahun > $tahun_akhir;
                                $style = $isLuarUmur ? 'background-color: #FF0000; padding: 2px 6px; border-radius: 4px;' : '';
                            @endphp
                            <tr>
                                <td>{{ $row->tahun }}</td>

                                <td>
                                    <span style="{{ $style }}">
                                        Rp. {{ number_format($row->penyusutan, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    <span style="{{ $style }}">
                                        Rp. {{ number_format($row->akumulasi_penyusutan, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    <span style="{{ $style }}">
                                        Rp. {{ number_format($row->nilai_buku, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <a href="{{ route('depresiasi.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
