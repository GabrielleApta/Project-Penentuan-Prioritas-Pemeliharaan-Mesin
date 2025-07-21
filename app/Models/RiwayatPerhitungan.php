<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RiwayatPerhitungan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_perhitungan';

    protected $fillable = [
        'kode_perhitungan',
        'metode_perhitungan',
        'tanggal_generate',
        'data_perhitungan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'data_perhitungan' => 'array',
        'tanggal_generate' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // Generate kode perhitungan unik
    public static function generateKodePerhitungan($metode)
    {
        $prefix = $metode === 'straight_line' ? 'SL' : 'SAW';
        $tahun = date('Y');

        // Cari nomor urut terakhir untuk tahun ini
        $lastRecord = self::where('metode_perhitungan', $metode)
            ->where('kode_perhitungan', 'LIKE', $prefix . '-' . $tahun . '-%')
            ->orderBy('kode_perhitungan', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = intval(substr($lastRecord->kode_perhitungan, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $tahun . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Accessor untuk format tanggal
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal_generate->format('d/m/Y H:i');
    }

    // Accessor untuk nama metode yang lebih readable
    public function getNamaMetodeAttribute()
    {
        return $this->metode_perhitungan === 'straight_line' ? 'Straight Line' : 'SAW';
    }
}
