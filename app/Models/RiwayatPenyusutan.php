<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenyusutan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_penyusutan';

    protected $fillable = [
        'mesin_id',
        'tahun',
        'umur_ekonomis',
        'nilai_sisa',
        'akumulasi_penyusutan',
        'nilai_buku',
        'kode_perhitungan',
        'dibuat_oleh',
        'tanggal_generate',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }
}
