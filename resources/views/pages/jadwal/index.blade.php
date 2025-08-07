@extends('layouts.app')

@section('title', 'Daftar Jadwal Pemeliharaan')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Jadwal Pemeliharaan Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Jadwal Pemeliharaan</li>
    </ol>

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

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Cek apakah ada data mesin --}}
    @if(\App\Models\Mesin::count() == 0)
        <div class="alert alert-warning d-flex align-items-start">
            <i class="fas fa-exclamation-triangle text-warning me-3 mt-1 fa-lg"></i>
            <div>
                <strong>Belum ada data mesin!</strong><br>
                Silakan tambahkan data mesin terlebih dahulu di menu <strong>Data Mesin</strong> sebelum membuat jadwal pemeliharaan.
            </div>
        </div>
    @endif

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-calendar-alt me-1"></i> Tabel Jadwal Pemeliharaan</div>
            <div>
                @if(auth()->user()->role === 'regu_mekanik')
                    @if(\App\Models\Mesin::count() > 0)
                        <a href="{{ route('jadwal.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Tambah Manual
                        </a>
                        <a href="{{ route('jadwal.generate.saw') }}" class="btn btn-primary btn-sm"
                           onclick="return confirm('Generate otomatis dari skor SAW?')">
                            <i class="fas fa-magic"></i> Generate Otomatis
                        </a>
                    @else
                        <button class="btn btn-secondary btn-sm" disabled title="Tambahkan data mesin terlebih dahulu">
                            <i class="fas fa-plus"></i> Tambah Manual
                        </button>
                        <button class="btn btn-secondary btn-sm" disabled title="Tambahkan data mesin terlebih dahulu">
                            <i class="fas fa-magic"></i> Generate Otomatis
                        </button>
                    @endif
                @endif

                {{-- Tambah tombol input histori --}}
                <a href="{{ route('history-pemeliharaan.index') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-history"></i> Lihat Histori Pemeliharaan
                </a>

                @if($jadwals->count() > 0)
                    <a href="{{ route('jadwal.printPDF') }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                @else
                    <button class="btn btn-secondary btn-sm" disabled title="Tidak ada data untuk dicetak">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center align-middle">
                    <thead class="thead-dark text-nowrap">
                        <tr>
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Tanggal Jadwal</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Tanggal Selesai</th>
                            <th>Catatan Perbaikan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $index => $jadwal)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                {{-- ✅ PERBAIKAN: Cek apakah mesin ada --}}
                                <td>
                                    @if($jadwal->mesin)
                                        {{ $jadwal->mesin->nama_mesin }}
                                    @else
                                        <span class="text-danger">Mesin Tidak Ditemukan</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->format('d-m-Y') }}</td>
                                <td>
                                    @php
                                        $prioritasBadge = [
                                            'tinggi' => 'danger',
                                            'sedang' => 'warning',
                                            'rendah' => 'success'
                                        ][$jadwal->prioritas] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $prioritasBadge }}">
                                        {{ ucfirst($jadwal->prioritas) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusBadge = match($jadwal->status) {
                                            'terjadwal' => 'primary',
                                            'selesai' => 'success',
                                            'terlambat' => 'danger',
                                            'batal' => 'secondary',
                                            default => 'dark'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusBadge }}">
                                        {{ ucfirst($jadwal->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $jadwal->tanggal_selesai
                                        ? \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d-m-Y')
                                        : '-' }}
                                </td>
                                <td>{{ $jadwal->catatan ?? '-' }}</td>
                                <td>
                                    @if($jadwal->mesin)
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#modalEditJadwal{{ $jadwal->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        @include('pages.jadwal.modal_edit', ['jadwal' => $jadwal])
                                    @else
                                        <span class="text-muted">Data Tidak Valid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4">
                                    @if(\App\Models\Mesin::count() == 0)
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Belum ada data mesin. Silakan tambahkan data mesin terlebih dahulu.
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-times"></i>
                                            Belum ada jadwal pemeliharaan. Klik tombol "Tambah Manual" atau "Generate Otomatis" untuk membuat jadwal.
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ Informasi tambahan jika ada data tidak valid --}}
            @if($jadwals->contains(function($jadwal) { return !$jadwal->mesin; }))
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Ditemukan beberapa jadwal dengan data mesin yang tidak valid.
                    Sistem akan otomatis membersihkan data yang tidak valid pada saat refresh halaman.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
