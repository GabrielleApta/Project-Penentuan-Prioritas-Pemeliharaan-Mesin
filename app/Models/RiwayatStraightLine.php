<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStraightLine extends Model
{
    use HasFactory;

    protected $table = 'riwayat_straight_line';

    protected $fillable = [
        'kode_perhitungan',
        'mesin_id',
        'tahun',
        'penyusutan',
        'akumulasi_penyusutan',
        'nilai_buku',
        'dibuat_oleh',
        'tanggal_generate'
    ];

    // Relasi ke mesin
    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
