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
        'email_verified_at',
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
            'kata_sandi'        => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
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

    /* ---- Reputasi & Prioritas Akun ---- */

    /**
     * Hitung jumlah pembatalan yang dilakukan peminjam dalam 60 hari terakhir.
     */
    public function getJumlahPembatalanAttribute(): int
    {
        return $this->peminjaman()
            ->where('status_pengajuan', 'dibatalkan')
            ->where('updated_at', '>=', now()->subDays(60))
            ->count();
    }

    /**
     * Dapatkan detail reputasi dan level prioritas akun peminjam.
     */
    public function getReputasiPrioritasAttribute(): array
    {
        $jumlahBatal = $this->jumlah_pembatalan;
        $isDosen = $this->jenis_pengguna === 'dosen';

        // Level 3: Prioritas Rendah (Batal >= 4 kali)
        if ($jumlahBatal >= 4) {
            return [
                'tingkat'      => 'rendah',
                'label'        => 'Prioritas Rendah',
                'badge_class'  => 'bg-rose-50 text-rose-700 border-rose-200',
                'badge_label'  => 'Prioritas Rendah (' . $jumlahBatal . 'x Batal)',
                'icon'         => 'alert-triangle',
                'warna'        => 'rose',
                'skor_antrean' => 10,
                'keterangan'   => 'Prioritas antrean terendah karena telah membatalkan peminjaman sebanyak ' . $jumlahBatal . ' kali dalam 60 hari terakhir.',
                'jumlah_batal' => $jumlahBatal,
            ];
        }

        // Level 2: Prioritas Diturunkan / Perhatian (Batal 3 kali)
        if ($jumlahBatal === 3) {
            return [
                'tingkat'      => 'perhatian',
                'label'        => 'Prioritas Diturunkan',
                'badge_class'  => 'bg-amber-50 text-amber-800 border-amber-200',
                'badge_label'  => 'Prioritas Diturunkan (3x Batal)',
                'icon'         => 'alert-circle',
                'warna'        => 'amber',
                'skor_antrean' => $isDosen ? 50 : 30,
                'keterangan'   => 'Prioritas antrean diturunkan karena telah membatalkan peminjaman sebanyak 3 kali dalam 60 hari terakhir.',
                'jumlah_batal' => $jumlahBatal,
            ];
        }

        // Level 1: Prioritas Normal / Tinggi (Batal 0 - 2 kali)
        return [
            'tingkat'      => $isDosen ? 'tinggi' : 'normal',
            'label'        => $isDosen ? 'Prioritas Dosen' : 'Prioritas Normal',
            'badge_class'  => $isDosen ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'badge_label'  => $isDosen ? 'Prioritas Dosen' : ($jumlahBatal > 0 ? 'Prioritas Normal (' . $jumlahBatal . 'x Batal)' : 'Prioritas Normal'),
            'icon'         => $isDosen ? 'award' : 'check-circle-2',
            'warna'        => $isDosen ? 'blue' : 'emerald',
            'skor_antrean' => $isDosen ? 100 : 80,
            'keterangan'   => $isDosen
                ? 'Akun Dosen dengan hak prioritas akademik standar.'
                : ($jumlahBatal > 0
                    ? 'Akun memiliki riwayat ' . $jumlahBatal . 'x pembatalan (masih dalam batas toleransi wajar).'
                    : 'Akun terpercaya dengan rekam jejak peminjaman tertib tanpa pembatalan.'),
            'jumlah_batal' => $jumlahBatal,
        ];
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
