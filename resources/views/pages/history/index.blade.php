@extends('layouts.app')

@section('title', 'Histori Pemeliharaan')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Histori Pemeliharaan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal Pemeliharaan</a></li>
        <li class="breadcrumb-item active">Histori Pemeliharaan</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <div class="alert alert-info d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
                <div>
                    Histori pemeliharaan di bawah ini digunakan untuk menelusuri catatan perawatan tiap mesin dan mendukung perhitungan prioritas pemeliharaan.<br>
                    Pastikan data seperti tanggal, jenis pemeliharaan, dan teknisi sudah diisi dengan benar.
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-tools me-1"></i> Tabel Histori Pemeliharaan</div>
            <div>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('history-pemeliharaan.create') }}" class="btn btn-primary btn-sm">
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
                            <th>Nama Mesin</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Deskripsi</th>
                            <th>Durasi (jam)</th>
                            <th>Teknisi</th>
                            <th>Foto Bukti</th>
                            <th>Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $index => $h)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start">{{ $h->mesin->nama_mesin ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('d M Y') }}</td>
                            <td>{{ ucfirst($h->jenis_pemeliharaan) }}</td>
                            <td>{{ $h->deskripsi }}</td>
                            <td>{{ $h->durasi_jam }} jam</td>
                            <td>{{ $h->teknisi }}</td>
                            <td>
                                @if($h->foto_bukti)
                                    <a href="{{ asset('storage/' . $h->foto_bukti) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $h->foto_bukti) }}" width="60" class="img-thumbnail">
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($h->verifikasi)
                                    <span class="badge bg-success">Terverifikasi</span>
                                @else
                                    <span class="badge bg-secondary">Belum</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $h->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('history-pemeliharaan.destroy', $h->id) }}" method="POST" class="formHapusHistory">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btnHapusHistory">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="editModal{{ $h->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $h->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('history-pemeliharaan.update', $h->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content shadow">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $h->id }}">Edit Histori Pemeliharaan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="mesin_id" class="form-select" required>
                                                            <option value="">Pilih Mesin</option>
                                                            @foreach($mesins as $mesin)
                                                                <option value="{{ $mesin->id }}" {{ $h->mesin_id == $mesin->id ? 'selected' : '' }}>{{ $mesin->nama_mesin }}</option>
                                                            @endforeach
                                                        </select>
                                                        <label>Nama Mesin</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="date" name="tanggal" value="{{ $h->tanggal }}" class="form-control" required>
                                                        <label>Tanggal</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <select name="jenis_pemeliharaan" class="form-select" required>
                                                            <option value="rutin" {{ $h->jenis_pemeliharaan == 'rutin' ? 'selected' : '' }}>Rutin</option>
                                                            <option value="perbaikan" {{ $h->jenis_pemeliharaan == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                                        </select>
                                                        <label>Jenis Pemeliharaan</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="number" step="0.1" name="durasi_jam" class="form-control" value="{{ $h->durasi_jam }}" required>
                                                        <label>Durasi (jam)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" name="teknisi" class="form-control" value="{{ $h->teknisi }}" required>
                                                        <label>Teknisi</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <textarea name="deskripsi" class="form-control" style="height: 100px;" required>{{ $h->deskripsi }}</textarea>
                                                        <label>Deskripsi</label>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Upload Ulang Foto (Opsional)</label>
                                                        <input type="file" name="foto_bukti" class="form-control">
                                                        <small class="text-muted">Biarkan kosong jika tidak mengubah.</small>
                                                    </div>
                                                    <div class="form-check mb-3">
                                                        <input type="checkbox" name="verifikasi" class="form-check-input" id="verifikasi{{ $h->id }}" {{ $h->verifikasi ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="verifikasi{{ $h->id }}">Terverifikasi</label>
                                                    </div>
                                                </div>
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
                        @endforeach

                        @if($histories->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center">Belum ada histori pemeliharaan.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            responsive: true,
            scrollX: true,
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
            lengthMenu: [[5, 10, 15, 25, -1], [5, 10, 15, 25, "Semua"]],
            pageLength: 10
        });
    });

    document.querySelectorAll('.btnHapusHistory').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Histori?',
                text: 'Data ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
