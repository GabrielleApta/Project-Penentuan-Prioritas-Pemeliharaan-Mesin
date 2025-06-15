<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prioritas extends Model
{
    use HasFactory;

    protected $table = 'hasil_saw';

    protected $fillable = [
        'mesin_id',
        'skor_akhir',
        'rangking'
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id');
    }
}
