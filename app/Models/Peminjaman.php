<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'pengguna_id',
        'fasilitas_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tujuan_peminjaman',
        'jumlah_peserta',
        'status_pengajuan',
        'alasan_penolakan',
        'catatan_persetujuan',
        'berkas_persyaratan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

    public function ulasan(): HasOne
    {
        return $this->hasOne(UlasanLayanan::class, 'peminjaman_id');
    }

    /**
     * Dapatkan detail reputasi dan prioritas dari pemohon peminjaman ini.
     */
    public function getReputasiPemohonAttribute(): array
    {
        return $this->pengguna ? $this->pengguna->reputasi_prioritas : [
            'tingkat'      => 'normal',
            'label'        => 'Prioritas Normal',
            'badge_class'  => 'bg-gray-100 text-gray-700 border-gray-200',
            'badge_label'  => 'Normal',
            'icon'         => 'check-circle-2',
            'warna'        => 'gray',
            'skor_antrean' => 80,
            'keterangan'   => '',
            'jumlah_batal' => 0,
        ];
    }
}
