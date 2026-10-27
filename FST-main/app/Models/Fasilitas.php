<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';

    protected $fillable = [
        'kategori_id',
        'kode_fasilitas',
        'nama_fasilitas',
        'status',
        'kondisi_awal',
        'deskripsi',
        'gambar',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriFasilitas::class, 'kategori_id');
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'fasilitas_id');
    }

    /**
     * Cek apakah fasilitas tersedia pada rentang waktu tertentu.
     */
    public function tersediaPadaWaktu(string $mulai, string $selesai, ?int $kecualiPeminjamanId = null): bool
    {
        $query = $this->peminjaman()
            ->whereIn('status_pengajuan', ['menunggu', 'disetujui'])
            ->where(function ($q) use ($mulai, $selesai) {
                $q->where(function ($q2) use ($mulai, $selesai) {
                    $q2->where('tanggal_mulai', '<', $selesai)
                        ->where('tanggal_selesai', '>', $mulai);
                });
            });

        if ($kecualiPeminjamanId) {
            $query->where('id', '!=', $kecualiPeminjamanId);
        }

        return $query->doesntExist();
    }
}
