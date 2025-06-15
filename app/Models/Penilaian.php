<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'mesin_id',
        'tahun',
        'nilai_penyusutan',
        'nilai_usia',
        'nilai_frekuensi',
        'nilai_downtime',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }
}
