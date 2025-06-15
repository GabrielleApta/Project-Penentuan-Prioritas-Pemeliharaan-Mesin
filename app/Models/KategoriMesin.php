<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriMesin extends Model
{
    use HasFactory;

    protected $table = 'kategori_mesin';

    protected $fillable = ['nama_kategori'];

    /**
     * Relasi ke tabel mesin (satu kategori memiliki banyak mesin)
     */
    public function mesin()
    {
        return $this->hasMany(Mesin::class, 'kategori_id', 'id');
    }
}
