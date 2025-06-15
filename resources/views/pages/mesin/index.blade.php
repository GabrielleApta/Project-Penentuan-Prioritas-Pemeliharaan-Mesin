@extends('layouts.app')

@section('title', 'Data Mesin')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Data Mesin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Mesin</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            Data mesin produksi benang yang digunakan dalam proses penilaian prioritas pemeliharaan.
        </div>
    </div>

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Data Mesin</div>
        </div>

        <div class="card-body">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('mesin.create') }}" class="btn btn-primary mb-3">
                    <i class="fas fa-plus"></i> Tambah Mesin
                </a>

                <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#importMesinModal">
                    <i class="fas fa-file-import"></i> Import Mesin
                </button>
            @endif

            <a href="{{ route('mesin.exportExcel') }}" class="btn btn-success mb-3">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>

            <a href="{{ route('mesin.mesin_pdf') }}" class="btn btn-danger mb-3" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-hover table-striped border text-center align-middle">
                    <thead class="thead-dark text-nowrap">
                        <tr>
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Kode Mesin</th>
                            <th>Tahun Pembelian</th>
                            <th>Spesifikasi Mesin</th>
                            <th>Daya Motor</th>
                            <th>Lokasi Mesin</th>
                            @if(auth()->user()->role === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mesin as $index => $m)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start">{{ $m->nama_mesin }}</td>
                                <td>{{ $m->kode_mesin }}</td>
                                <td>{{ $m->tahun_pembelian }}</td>
                                <td class="text-start">{{ $m->spesifikasi_mesin }}</td>
                                <td>
                                    {{ (fmod($m->daya_motor, 1) != 0) ? str_replace('.', ',', rtrim($m->daya_motor, '0')) : (int) $m->daya_motor }} KW
                                </td>
                                <td>{{ $m->lokasi_mesin }}</td>
                                @if(auth()->user()->role === 'admin')
                                    <td class="text-nowrap">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('mesin.edit', $m->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('mesin.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach

                        @if ($mesin->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data mesin.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            pagingType: "full_numbers",
            columnDefs: [{ orderable: false, targets: -1 }],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "›",
                    previous: "‹"
                }
            }
        });
    });
</script>
@endsection

{{-- Modal Import Mesin --}}
<div class="modal fade" id="importMesinModal" tabindex="-1" aria-labelledby="importMesinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formImportMesin" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importMesinModalLabel">Import Data Mesin (Excel)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_mesin" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" name="file" id="file_mesin" accept=".xlsx, .xls" required>
                    </div>
                    <small class="text-muted">Format yang didukung: .xls, .xlsx</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('formImportMesin').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        Swal.fire({
            title: 'Import Mesin?',
            text: "Pastikan file Excel sudah sesuai format!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Import!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post("{{ route('mesin.import') }}", formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                .then(response => {
                    Swal.fire('Sukses!', 'Data mesin berhasil diimpor.', 'success')
                        .then(() => location.reload());
                })
                .catch(error => {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat mengimpor.', 'error');
                    console.error(error);
                });
            }
        });
    });
</script>
@endpush
