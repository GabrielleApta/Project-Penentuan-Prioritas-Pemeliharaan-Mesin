<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPemeliharaan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pemeliharaan';

    protected $fillable = [
        'mesin_id',
        'tanggal_jadwal',
        'prioritas',
        'catatan',
        'status',
        'tanggal_selesai',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }
}
