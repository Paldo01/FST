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
        'kapasitas',
        'jumlah',
        'deskripsi',
        'gambar',
    ];

    public function getJumlahFormattedAttribute(): string
    {
        if (!empty($this->jumlah)) {
            return str_contains(strtolower($this->jumlah), 'unit') 
                ? $this->jumlah 
                : $this->jumlah . ' Unit';
        }

        return '1 Unit';
    }

    public function getKapasitasFormattedAttribute(): string
    {
        if (!empty($this->kapasitas)) {
            return str_contains(strtolower($this->kapasitas), 'orang') 
                ? $this->kapasitas 
                : $this->kapasitas . ' Orang';
        }

        if (!empty($this->deskripsi) && preg_match('/(\d+)\s*(orang|kursi|unit)/i', $this->deskripsi, $matches)) {
            return $matches[1] . ' Orang';
        }

        return '-';
    }

    public function getLokasiAttribute(): string
    {
        $nama = strtolower($this->nama_fasilitas);
        if (str_contains($nama, 'aula')) {
            return 'Gedung FST Lantai 3';
        } elseif (str_contains($nama, 'lab') || str_contains($nama, 'komputer')) {
            return 'Gedung FST Lantai 1';
        } elseif (str_contains($nama, 'rapat')) {
            return 'Gedung Dekanat Lantai 2';
        } else {
            return 'Gedung FST Lantai 2';
        }
    }

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
