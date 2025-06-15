<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSaw extends Model
{
    use HasFactory;

    protected $table = 'hasil_saw';

    protected $fillable = [
        'mesin_id', 'skor_akhir', 'ranking'
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }
}
