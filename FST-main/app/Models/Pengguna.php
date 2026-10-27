<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'nomor_identitas',
        'nomor_telepon',
        'kata_sandi',
        'peran',
        'jenis_pengguna',
        'status_akun',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    /**
     * Get the password attribute name for authentication.
     */
    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
        ];
    }

    /* ---- Role Helpers ---- */

    public function isSuperAdmin(): bool
    {
        return $this->peran === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isPengguna(): bool
    {
        return $this->peran === 'pengguna';
    }

    /* ---- Relationships ---- */

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'pengguna_id');
    }

    public function ulasanLayanan(): HasMany
    {
        return $this->hasMany(UlasanLayanan::class, 'pengguna_id');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'pengguna_id');
    }

    public function pengembalianDiproses(): HasMany
    {
        return $this->hasMany(Pengembalian::class, 'admin_id');
    }
}
