<!-- Modal Edit Histori Pemeliharaan -->
<div class="modal fade" id="editModal-{{ $history->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $history->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('history-pemeliharaan.update', $history->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Histori Pemeliharaan - {{ $history->mesin->nama_mesin ?? '-' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body row">
                    <div class="mb-3 col-md-6">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $history->tanggal }}" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="jenis_pemeliharaan" class="form-label">Jenis Pemeliharaan</label>
                        <input type="text" name="jenis_pemeliharaan" class="form-control" value="{{ $history->jenis_pemeliharaan }}" required>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" required>{{ $history->deskripsi }}</textarea>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="durasi_jam" class="form-label">Durasi (jam)</label>
                        <input type="number" name="durasi_jam" class="form-control" value="{{ $history->durasi_jam }}" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="teknisi" class="form-label">Teknisi</label>
                        <input type="text" name="teknisi" class="form-control" value="{{ $history->teknisi }}" required>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label for="foto_bukti" class="form-label">Foto Bukti (Opsional)</label><br>
                        @if ($history->foto_bukti)
                            <img src="{{ asset('storage/' . $history->foto_bukti) }}" alt="Foto Bukti" class="img-fluid mb-2" style="max-height: 150px;">
                        @endif
                        <input type="file" name="foto_bukti" class="form-control">
                    </div>

                    <div class="mb-3 col-md-12">
                        <label for="verifikasi" class="form-label">Verifikasi</label>
                        <select name="verifikasi" class="form-select" required>
                            <option value="belum" {{ $history->verifikasi == 'belum' ? 'selected' : '' }}>Belum Diverifikasi</option>
                            <option value="sudah" {{ $history->verifikasi == 'sudah' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
