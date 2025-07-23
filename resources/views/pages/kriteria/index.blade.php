@extends('layouts.app')

@section('title', 'Kriteria dan Bobot')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Data Kriteria dan Bobot</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Kriteria dan Bobot</li>
    </ol>

    <div class="card mb-4">
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-start">
            <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
            <div>
                <strong>Informasi:</strong><br>
                Kriteria di bawah ini digunakan sebagai dasar perhitungan dalam metode <strong>SAW (Simple Additive Weighting)</strong> untuk menentukan prioritas pemeliharaan mesin.
                <ul class="mb-0 mt-1">
                    <li><strong>Bobot</strong> menunjukkan tingkat kepentingan kriteria (harus total 1 atau 100%).</li>
                    <li><strong>Jenis kriteria</strong> menentukan arah penilaian:
                        <ul>
                            <li><em>Benefit</em> = semakin tinggi nilai, semakin baik.</li>
                            <li><em>Cost</em> = semakin rendah nilai, semakin baik.</li>
                        </ul>
                    </li>
                    <li>Edit atau hapus data kriteria dengan bijak, karena akan mempengaruhi hasil perhitungan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>


    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Data Kriteria</div>
        <div>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('kriteria.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
            @endif
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
                <table id="dataTable" class="table table-striped table-bordered text-center align-middle" style="width:100%">
                    <thead class="thead-dark text-nowrap">
                        <tr>
                            <th>No</th>
                            <th>Nama Kriteria</th>
                            <th>Bobot</th>
                            <th>Jenis</th>
                            @if(auth()->user()->role === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kriteria as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_kriteria }}</td>
                                <td>{{ $item->bobot * 100 }}%</td>
                                <td>{{ ucfirst($item->jenis_kriteria) }}</td>
                                @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditKriteria{{ $item->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('kriteria.destroy', $item->id) }}" method="POST" class="formHapusKriteria d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btnHapusKriteria">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>

                            {{-- Modal Edit --}}
                            <div class="modal fade" id="modalEditKriteria{{ $item->id }}" tabindex="-1" aria-labelledby="editKriteriaLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('kriteria.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editKriteriaLabel{{ $item->id }}">Edit Kriteria</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
                                                    <input type="text" name="nama_kriteria" class="form-control" value="{{ $item->nama_kriteria }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="bobot" class="form-label">Bobot (0–1)</label>
                                                    <input type="number" name="bobot" class="form-control" step="0.01" min="0" max="1" value="{{ $item->bobot }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="jenis_kriteria" class="form-label">Jenis</label>
                                                    <select name="jenis_kriteria" class="form-select" required>
                                                        <option value="benefit" {{ $item->jenis_kriteria == 'benefit' ? 'selected' : '' }}>Benefit</option>
                                                        <option value="cost" {{ $item->jenis_kriteria == 'cost' ? 'selected' : '' }}>Cost</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-warning">Update</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="5">Tidak ada data kriteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hapusButtons = document.querySelectorAll('.btnHapusKriteria');
        hapusButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Kriteria?',
                    text: 'Data akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


@endpush
