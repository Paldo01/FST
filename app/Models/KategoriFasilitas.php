<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriFasilitas extends Model
{
    protected $table = 'kategori_fasilitas';

    protected $fillable = [
        'nama_kategori',
        'jenis',
        'deskripsi',
    ];

    public function fasilitas(): HasMany
    {
        return $this->hasMany(Fasilitas::class, 'kategori_id');
    }
}
