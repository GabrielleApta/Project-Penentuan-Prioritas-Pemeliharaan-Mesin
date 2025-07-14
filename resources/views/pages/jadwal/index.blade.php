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
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-calendar-alt me-1"></i> Tabel Jadwal Pemeliharaan</div>
            @if(auth()->user()->role === 'admin')
                <div>
                    <a href="{{ route('jadwal.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Tambah Manual
                    </a>
                    <a href="{{ route('jadwal.generate.saw') }}" class="btn btn-primary btn-sm"
                       onclick="return confirm('Generate otomatis dari skor SAW?')">
                        <i class="fas fa-magic"></i> Generate Otomatis
                    </a>
                </div>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center">
                    <thead class="thead-dark text-nowrap">
                        <tr>
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Kode</th>
                            <th>Tanggal Jadwal</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Tanggal Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $index => $jadwal)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $jadwal->mesin->nama_mesin }}</td>
                                <td>{{ $jadwal->mesin->kode_mesin }}</td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->format('d-m-Y') }}</td>
                                <td>
                                    @php
                                        $badge = [
                                            'tinggi' => 'danger',
                                            'sedang' => 'warning',
                                            'rendah' => 'success'
                                        ][$jadwal->prioritas];
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($jadwal->prioritas) }}</span>
                                </td>
                                <td>
                                    @php
                                        $badge = match($jadwal->status) {
                                            'terjadwal' => 'primary',
                                            'selesai' => 'success',
                                            'terlambat' => 'danger',
                                            'batal' => 'secondary',
                                            default => 'dark'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($jadwal->status) }}</span>
                                </td>
                                <td>
                                    {{ $jadwal->tanggal_selesai ? \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    {{-- Step 4: nanti bisa ada tombol ubah status di sini --}}
                                    <a href="#" class="btn btn-info btn-sm" disabled><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">Belum ada jadwal pemeliharaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
