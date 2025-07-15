<div class="modal fade" id="modalEditJadwal{{ $jadwal->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $jadwal->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('jadwal.updateStatus', $jadwal->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel{{ $jadwal->id }}">Edit Status Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select" name="status" required>
                                    <option value="terjadwal" {{ $jadwal->status == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                    <option value="selesai" {{ $jadwal->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="terlambat" {{ $jadwal->status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                    <option value="batal" {{ $jadwal->status == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                                <label>Status Jadwal</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" name="tanggal_selesai"
                                       value="{{ $jadwal->tanggal_selesai ? \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('Y-m-d') : '' }}">
                                <label>Tanggal Selesai</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="catatan"
                                        value="{{ old('catatan', $jadwal->catatan) }}">
                                <label>Catatan Perbaikan</label>
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
