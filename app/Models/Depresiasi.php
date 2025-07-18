<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depresiasi extends Model
{
    use HasFactory;

    protected $table = 'penyusutan_mesin';

    protected $fillable = [
        'mesin_id', 'tahun', 'penyusutan', 'akumulasi_penyusutan', 'nilai_buku',
    ];

    public function mesin()
{
    return $this->belongsTo(Mesin::class);
}

public function riwayat()
{
    return $this->hasOne(RiwayatStraightLine::class, 'mesin_id');
}
}
