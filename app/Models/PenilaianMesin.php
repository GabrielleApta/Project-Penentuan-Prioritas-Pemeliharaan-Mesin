<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianMesin extends Model
{
    use HasFactory;

    protected $table = 'penilaian_mesin';

    protected $fillable = [
        'mesin_id',
        'akumulasi_penyusutan',
        'usia_mesin',
        'frekuensi_kerusakan',
        'waktu_downtime',
        'tahun_penilaian',
    ];

    // ✅ EXPLICIT FOREIGN KEY AND LOCAL KEY
    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id', 'id');
    }
}
