@extends('layouts.app')

@section('title', 'Data Kerusakan Tahunan')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Data Kerusakan Tahunan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Kerusakan Tahunan</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <div class="alert alert-info d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1 fa-lg"></i>
                <div>
                    Data ini digunakan untuk menghitung prioritas pemeliharaan mesin dengan metode
                    <strong>Straight Line</strong> dan <strong>SAW</strong>. Pastikan semua data kerusakan mesin tahunannya lengkap dan valid.
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-table me-1"></i> Tabel Kerusakan Tahunan</div>
        </div>

        <div class="card-body">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('kerusakan-tahunan.create') }}" class="btn btn-primary mb-3">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-outline-secondary mb-3" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import"></i> Import Data
                </button>
            @endif

            <a href="{{ route('kerusakan-tahunan.exportExcel') }}" class="btn btn-outline-success mb-3">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>

            <button type="button" class="btn btn-outline-danger mb-3" data-bs-toggle="modal" data-bs-target="#modalFilterPDF">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div style="table-responsive">
                <table id="dataTable" class="table table-striped table-bordered text-center align-middle" style="width:100%">
                    <thead class="thead-dark text-nowrap">
                        <tr>
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Tahun</th>
                            <th>Kerusakan Ringan</th>
                            <th>Downtime Ringan (jam)</th>
                            <th>Kerusakan Parah</th>
                            <th>Downtime Parah (jam)</th>
                            @if(auth()->user()->role === 'admin')
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start">{{ $item->mesin->nama_mesin }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>{{ $item->kerusakan_ringan }}</td>
                                <td>{{ number_format($item->downtime_ringan, 1, ',', '.') }}</td>
                                <td>{{ $item->kerusakan_parah }}</td>
                                <td>{{ number_format($item->downtime_parah, 1, ',', '.') }}</td>
                                @if(auth()->user()->role === 'admin')
                                    <td class="text-nowrap">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditKerusakan{{ $item->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="{{ route('kerusakan-tahunan.destroy', $item->id) }}" method="POST" class="d-inline formHapusKerusakan">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btnHapusKerusakan">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach

                        @if ($data->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data kerusakan tahunan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formImportKerusakan" method="POST" action="{{ route('kerusakan.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Kerusakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <label for="file_kerusakan" class="form-label">Pilih File Excel</label>
                    <input type="file" class="form-control" name="file" id="file_kerusakan" accept=".xls,.xlsx,.csv" required>
                    <small class="text-muted">Format yang didukung: .xls, .xlsx, .csv</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Import</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Filter PDF --}}
<div class="modal fade" id="modalFilterPDF" tabindex="-1" aria-labelledby="filterPDFLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('kerusakan-tahunan.exportPdfFiltered') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterPDFLabel">Export PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="number" class="form-control" name="tahun" placeholder="Contoh: 2024">
                    </div>
                    <div class="mb-3">
                        <label for="mesin_id" class="form-label">Nama Mesin</label>
                        <select name="mesin_id" class="form-select">
                            <option value="">-- Pilih Mesin --</option>
                            @foreach(\App\Models\Mesin::orderBy('nama_mesin')->get() as $mesin)
                                <option value="{{ $mesin->id }}">{{ $mesin->nama_mesin }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Export PDF</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit (semua dipindah ke luar tabel) --}}
@foreach ($data as $item)
    <div class="modal fade" id="modalEditKerusakan{{ $item->id }}" tabindex="-1" aria-labelledby="editKerusakanLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('kerusakan-tahunan.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editKerusakanLabel{{ $item->id }}">Edit Data: {{ $item->mesin->nama_mesin }} ({{ $item->tahun }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="{{ $item->mesin->nama_mesin }}" disabled>
                                    <label>Nama Mesin</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" name="tahun" value="{{ $item->tahun }}" required>
                                    <label>Tahun</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" name="kerusakan_ringan" value="{{ $item->kerusakan_ringan }}" required>
                                    <label>Kerusakan Ringan</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" name="downtime_ringan" value="{{ $item->downtime_ringan }}" required>
                                    <label>Downtime Ringan (jam)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" name="kerusakan_parah" value="{{ $item->kerusakan_parah }}" required>
                                    <label>Kerusakan Parah</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" name="downtime_parah" value="{{ $item->downtime_parah }}" required>
                                    <label>Downtime Parah (jam)</label>
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
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                scrollX: true,
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
                pageLength: 10
            });

            $('.btnHapusKerusakan').on('click', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Data akan dihapus secara permanen!',
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
