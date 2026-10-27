<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id',
        'admin_id',
        'tanggal_pengembalian',
        'kondisi_akhir',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengembalian' => 'datetime',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'admin_id');
    }
}
