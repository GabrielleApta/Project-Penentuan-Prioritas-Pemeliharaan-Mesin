@extends('layouts.app')

@section('title', 'Perhitungan Penyusutan Mesin')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Perhitungan Penyusutan Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active"> Perhitungan Penyusutan Mesin</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <div class="alert alert-info d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
                <div>
                    Metode Straight Line adalah metode penyusutan aset yang menghitung nilai penyusutan dengan jumlah yang sama setiap tahunnya selama masa manfaat aset tersebut. Cara kerjanya adalah dengan membagi selisih antara biaya perolehan aset dan nilai sisa dengan masa manfaat aset, sehingga beban penyusutan yang dibebankan tiap tahun konsisten dan tetap hingga aset tersebut mencapai nilai sisa atau akhir masa pakainya. Perhitungan ini menggunakan rumus :<br>
                    <strong>Depresiasi Tahunan = (Harga Pembelian - Nilai Sisa) / Umur Ekonomis</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Perhitungan Penyusutan Mesin</div>
        <div>
            @if(auth()->user()->role === 'regu_mekanik')
                <a href="{{ route('depresiasi.reset') }}" class="btn btn-primary btn-sm"
                    onclick="return confirm('Yakin ingin mereset semua data depresiasi?')">
                    <i class="fas fa-sync-alt"></i> Hitung Ulang
                </a>

                <form action="{{ route('riwayat.straight-line.simpan') }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-success btn-sm"
        onclick="return confirm('Yakin ingin menyimpan ke riwayat?')">
        <i class="fas fa-save"></i> Simpan ke Riwayat
    </button>
</form>
                <a href="{{ route('riwayat.straight-line.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel"></i> Riwayat Perhitungan
                </a>
            @endif
            <a href="{{ route('depresiasi.exportPdf') }}" class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle" style="width:100%">
                    <thead class="thead-dark text-nowrap text-center">
                        <tr>
                            <th>No</th>
                            <th class="text-start">Nama Mesin</th>
                            <th>Kode Mesin</th>
                            <th class="text-center text-nowrap">Harga Beli</th>
                            <th class="text-center text-nowrap">Nilai Sisa</th>
                            <th class="text-center">Umur Ekonomis</th>
                            <th class="text-center text-nowrap">Depresiasi Tahunan</th>
                            <th class="text-center text-nowrap">Akumulasi Penyusutan ({{ now()->year }})</th>
                            <th class="text-center text-nowrap">Nilai Buku ({{ now()->year }})</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mesins as $index => $m)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $m->nama_mesin }}</td>
                                <td class="text-center">{{ $m->kode_mesin }}</td>
                                <td class="text-center text-nowrap">Rp. {{ number_format($m->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-center text-nowrap">Rp. {{ number_format($m->nilai_sisa, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $m->umur_ekonomis }} tahun</td>
                                <td class="text-center text-nowrap">Rp. {{ number_format($m->depresiasi_tahunan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center text-nowrap">Rp. {{ number_format($m->total_akumulasi, 0, ',', '.') }}</td>
                                <td class="text-center text-nowrap">Rp. {{ number_format($m->nilai_buku_akhir, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('depresiasi.show', $m->id) }}" class="btn btn-info btn-sm">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data penyusutan mesin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
