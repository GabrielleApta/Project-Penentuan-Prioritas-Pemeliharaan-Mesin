<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatSaw extends Model
{
    use HasFactory;

    protected $table = 'riwayat_saw';

    protected $fillable = [
        'mesin_id',
        'kode_perhitungan',
        'nama_mesin',
        'akumulasi_penyusutan',
        'usia_mesin',
        'frekuensi_kerusakan',
        'waktu_downtime',
        'skor_akhir',
        'ranking',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    public function detail()
{
    return $this->hasOne(RiwayatSawDetail::class);
}
}
