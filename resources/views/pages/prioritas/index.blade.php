@extends('layouts.app')

@section('title', 'Perhitungan SAW')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Ranking Prioritas Pemeliharaan Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Hasil Perhitungan SAW</li>
    </ol>



    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Data Ranking Prioritas Pemeliharaan Mesin</div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                @if(auth()->user()->role === 'regu_mekanik')
                    <a href="{{ route('prioritas.hitung') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-calculator"></i> Hitung Ulang
                    </a>

                    <a href="{{ route('penilaian.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-table me-1"></i> Penilaian Mesin
                    </a>

                    <form action="{{ route('riwayat-saw.store') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"
                            onclick="return confirm('Simpan perhitungan ini ke riwayat?')">
                            <i class="fas fa-save"></i> Simpan ke Riwayat
                        </button>
                    </form>
                @endif

                <a href="{{ route('riwayat-saw.index') }}" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf"></i> Riwayat Perhitungan
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if($hasil_saw->isEmpty())
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Belum ada hasil perhitungan SAW.</strong><br>
                        <small>Pastikan data penilaian mesin sudah lengkap, lalu klik tombol "Hitung Ulang" untuk memulai perhitungan.</small>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table id="dataTable" class="table table-striped table-bordered text-center align-middle" style="width:100%">
                    <thead class="thead-dark text-nowrap">
                            <tr class="text-center">
                                <th class="text-center">Rangking</th>
                                <th class="text-center">Nama Mesin</th>
                                <th class="text-center">Skor Akhir</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hasil_saw as $hasil)
                                <tr>
                                    <td>
    {{ $loop->iteration }}
</td>
                                    <td class="text-center">
                                        @if($hasil->mesin)
                                            {{ $hasil->mesin->nama_mesin }}
                                        @else
                                            <span class="text-muted fst-italic">Mesin telah dihapus</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($hasil->skor_akhir, 4) }}</span>
                                    </td>
                                    <td>
                                        @if($hasil->mesin)
                                            <a href="{{ route('prioritas.detail', $hasil->mesin_id) }}" class="btn btn-info btn-sm">
                                                <i class="fa-solid fa-circle-info"></i> Detail
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled title="Tidak bisa melihat detail karena mesin sudah dihapus">
                                                <i class="fa-solid fa-circle-info"></i> Detail
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                scrollX: false,
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "→",
                        previous: "←"
                    },
                },
                lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "Semua"]],
                pageLength: 10,
                order: [[0, 'asc']] // Urutkan berdasarkan ranking
            });
        });
    </script>
@endpush
@endsection
