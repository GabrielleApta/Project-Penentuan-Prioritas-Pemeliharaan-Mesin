<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    use HasFactory;

    protected $table = 'mesin';

    protected $fillable = [
        'nama_mesin',
        'kode_mesin',
        'harga_beli',
        'tahun_pembelian',
        'spesifikasi_mesin',
        'daya_motor',
        'lokasi_mesin',
        'nilai_sisa',
        'umur_ekonomis',
        'status'
    ];

    protected $casts = [
        'daya_motor' => 'decimal:2',
    ];

    // Relasi ke tabel kerusakan tahunan
    public function kerusakanTahunan()
    {
    return $this->hasMany(KerusakanTahunan::class);
    }

    // Relasi ke tabel penilaian_mesin
    public function penilaian()
    {
        return $this->hasMany(PenilaianMesin::class, 'mesin_id');
    }

    // Relasi ke tabel hasil_saw
    public function hasilSaw()
    {
        return $this->hasOne(HasilSaw::class);
    }

    // Relasi ke tabel penyusutan_mesin
    public function depresiasi()
    {
        return $this->hasMany(Depresiasi::class);
    }

    // Relasi ke tabel jadwal_pemeliharaan
    public function jadwalPemeliharaan()
    {
    return $this->hasMany(JadwalPemeliharaan::class);
    }

    // Relasi ke tabel kategori_mesin
    public function kategori()
    {
        return $this->belongsTo(KategoriMesin::class, 'kategori_id', 'id');
    }

    // Menghapus data terkait di penilaian_mesin saat mesin dihapus
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($mesin) {
            $mesin->penilaian()->delete();
        });
    }

    public function skorFrekuensiKerusakan($tahunAwal = 2022, $tahunAkhir = 2024)
    {
    $totalSkor = 0;
    $jumlahTahun = $tahunAkhir - $tahunAwal + 1;

    for ($tahun = $tahunAwal; $tahun <= $tahunAkhir; $tahun++) {
        $data = $this->kerusakanTahunan()
                     ->where('tahun', $tahun)
                     ->first();

        if ($data) {
            $skorTahun = ($data->kerusakan_ringan * 1) + ($data->kerusakan_parah * 3);
        } else {
            $skorTahun = 0; // Jika tahun itu kosong
        }

        $totalSkor += $skorTahun;
    }

    return round($totalSkor / $jumlahTahun, 2); // dibulatkan 2 angka desimal
    }

    public function skorDowntime($tahunAwal = 2022, $tahunAkhir = 2024)
    {
    $totalDowntime = 0;
    $jumlahTahun = $tahunAkhir - $tahunAwal + 1;

    for ($tahun = $tahunAwal; $tahun <= $tahunAkhir; $tahun++) {
        $data = $this->kerusakanTahunan()->where('tahun', $tahun)->first();

        if ($data) {
            $downtimeTahun = ($data->downtime_ringan * 1) + ($data->downtime_parah * 3);
        } else {
            $downtimeTahun = 0;
        }

        $totalDowntime += $downtimeTahun;
    }

    return round($totalDowntime / $jumlahTahun, 2);
    }

}
