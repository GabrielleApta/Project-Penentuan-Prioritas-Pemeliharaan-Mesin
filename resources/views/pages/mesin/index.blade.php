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
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditMesin{{ $m->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="{{ route('mesin.destroy', $m->id) }}" method="POST" class="formHapusMesin d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btnHapusMesin">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>

                            {{-- Modal Edit Mesin --}}
                            <div class="modal fade" id="modalEditMesin{{ $m->id }}" tabindex="-1" aria-labelledby="editMesinLabel{{ $m->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('mesin.update', $m->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editMesinLabel{{ $m->id }}">Edit Mesin</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" name="nama_mesin" value="{{ $m->nama_mesin }}" required>
                                                            <label>Nama Mesin</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" name="kode_mesin" value="{{ $m->kode_mesin }}" required>
                                                            <label>Kode Mesin</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="number" class="form-control" name="harga_beli" value="{{ $m->harga_beli }}" required>
                                                            <label>Harga Beli</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="number" class="form-control" name="tahun_pembelian" value="{{ $m->tahun_pembelian }}" required>
                                                            <label>Tahun Pembelian</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" name="spesifikasi_mesin" value="{{ $m->spesifikasi_mesin }}" required>
                                                            <label>Spesifikasi Mesin</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="number" step="0.01" class="form-control" name="daya_motor" value="{{ $m->daya_motor }}" required>
                                                            <label>Daya Motor</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" name="lokasi_mesin" value="{{ $m->lokasi_mesin }}" required>
                                                            <label>Lokasi Mesin</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="number" class="form-control" name="nilai_sisa" value="{{ $m->nilai_sisa }}" required>
                                                            <label>Nilai Sisa</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <input type="number" class="form-control" name="umur_ekonomis" value="{{ $m->umur_ekonomis }}" required>
                                                            <label>Umur Ekonomis (tahun)</label>
                                                        </div>
                                                        <div class="form-floating mb-3">
                                                            <select class="form-select" name="status" required>
                                                                <option value="aktif" {{ $m->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                                <option value="tidak aktif" {{ $m->status == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                                            </select>
                                                            <label>Status Mesin</label>
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

{{-- Modal Import Mesin --}}
<div class="modal fade" id="importMesinModal" tabindex="-1" aria-labelledby="importMesinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formImportMesin" enctype="multipart/form-data">
            @csrf
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="importMesinModalLabel">Import Data Mesin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_mesin" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" name="file" id="file_mesin" accept=".xlsx, .xls" required>
                        <small class="text-muted">Format file: .xls, .xlsx</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Import</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

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

    document.addEventListener('DOMContentLoaded', function () {
        const hapusButtons = document.querySelectorAll('.btnHapusMesin');
        hapusButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Mesin?',
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
    });
</script>
@endpush
