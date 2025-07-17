<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPemeliharaan extends Model
{
    use HasFactory;

    protected $table = 'history_pemeliharaan';

    protected $fillable = [
        'mesin_id',
        'jadwal_id',
        'tanggal',
        'jenis_pemeliharaan',
        'deskripsi',
        'durasi_jam',
        'teknisi',
        'foto_bukti',
        'verifikasi'
    ];

     public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

}
