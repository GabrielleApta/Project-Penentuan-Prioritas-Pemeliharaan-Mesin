<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KerusakanTahunan extends Model
{
    use HasFactory;

    protected $table = 'kerusakan_tahunan';

    protected $fillable = [
         'mesin_id',
         'tahun',
         'kerusakan_ringan',
         'kerusakan_parah',
         'downtime_ringan',
         'downtime_parah',
         'skor_frekuensi_kerusakan',
         'skor_waktu_downtime',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    // Fungsi untuk menghitung total skor kerusakan tahunan
    public function skorKerusakan(): float
    {
        return ($this->kerusakan_ringan * 1) + ($this->kerusakan_parah * 3);
    }

    // Fungsi untuk menghitung total downtime tahunan
    public function totalDowntime(): float
    {
        return $this->downtime_ringan + $this->downtime_parah;
    }
}
