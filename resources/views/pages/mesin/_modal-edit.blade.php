<!-- Modal Edit Mesin -->
<div class="modal fade" id="modalEditMesin{{ $mesin->id }}" tabindex="-1" aria-labelledby="editMesinLabel{{ $mesin->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('mesin.update', $mesin->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMesinLabel{{ $mesin->id }}">Edit Mesin: {{ $mesin->nama_mesin }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Nama Mesin</label>
                        <input type="text" name="nama_mesin" class="form-control" value="{{ $mesin->nama_mesin }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>Kode Mesin</label>
                        <input type="text" name="kode_mesin" class="form-control" value="{{ $mesin->kode_mesin }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi_mesin" class="form-control" value="{{ $mesin->lokasi_mesin }}">
                    </div>
                    <div class="col-md-6">
                        <label>Tahun Pembelian</label>
                        <input type="number" name="tahun_pembelian" class="form-control" value="{{ $mesin->tahun_pembelian }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>Harga Beli</label>
                        <input type="number" name="harga_beli" class="form-control" value="{{ $mesin->harga_beli }}">
                    </div>
                    <div class="col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="aktif" {{ $mesin->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $mesin->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
