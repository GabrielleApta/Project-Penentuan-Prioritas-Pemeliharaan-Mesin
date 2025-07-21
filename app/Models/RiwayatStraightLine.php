<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStraightLine extends Model
{
    use HasFactory;

    protected $table = 'riwayat_straight_line';

    protected $fillable = [
        'mesin_id',
        'kode_perhitungan',
        'nama_mesin',
        'tahun_pembelian',
        'harga_beli',
        'nilai_sisa',
        'umur_ekonomis',
        'usia_mesin',
        'penyusutan_per_tahun',
        'akumulasi_penyusutan',
        'nilai_buku',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

     public static function generateKode()
    {
        $prefix = 'SL-' . date('Y') . '-';
        $last = self::where('kode_perhitungan', 'LIKE', $prefix . '%')
                    ->orderBy('kode_perhitungan', 'desc')
                    ->first();

        $lastNumber = $last ? (int) substr($last->kode_perhitungan, -3) : 0;
        return $prefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }
}
