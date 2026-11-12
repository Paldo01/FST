<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    public $timestamps = false;

    protected $fillable = [
        'pengguna_id',
        'tindakan',
        'detail_kegiatan',
        'alamat_ip',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    /**
     * Label representasi tindakan sesuai tampilan mockup.
     */
    public function getLabelTindakanAttribute(): string
    {
        $raw = trim($this->tindakan ?? '');
        $lower = strtolower($raw);

        return match ($lower) {
            'login' => 'Login',
            'logout' => 'Logout',
            'tambah_admin', 'menambah admin' => 'Menambah admin',
            'ajukan_peminjaman', 'mengajukan peminjaman' => 'Mengajukan Peminjaman',
            'setujui_peminjaman', 'menyetujui peminjaman' => 'Menyetujui Peminjaman',
            'tolak_peminjaman', 'menolak peminjaman' => 'Menolak Peminjaman',
            'ubah_admin', 'mengubah admin' => 'Mengubah Data Admin',
            'hapus_admin', 'menghapus admin' => 'Menghapus Data Admin',
            'ubah_fasilitas', 'ubah_ruang', 'mengubah data ruang' => 'Mengubah Data Ruang',
            'tambah_fasilitas', 'tambah_ruang', 'menambah data ruang' => 'Menambah Data Ruang',
            'hapus_fasilitas', 'hapus_ruang', 'menghapus data' => 'Menghapus Data',
            'lihat_laporan', 'melihat laporan' => 'Melihat Laporan',
            'registrasi' => 'Registrasi Akun',
            'proses_pengembalian' => 'Pengembalian Fasilitas',
            default => (str_contains($raw, '_') ? ucwords(str_replace('_', ' ', $raw)) : $raw)
        };
    }

    /**
     * CSS class warna badge pill sesuai rancangan desain mockup.
     */
    public function getBadgeClassAttribute(): string
    {
        $label = strtolower($this->label_tindakan);

        return match (true) {
            str_contains($label, 'login') => 'bg-[#DCFCE7] text-[#16A34A]',
            str_contains($label, 'menambah') || str_contains($label, 'tambah') => 'bg-[#DBEAFE] text-[#2563EB]',
            str_contains($label, 'mengajukan') || str_contains($label, 'ajukan') => 'bg-[#FEF3C7] text-[#D97706]',
            str_contains($label, 'menyetujui') || str_contains($label, 'setujui') => 'bg-[#D1FAE5] text-[#059669]',
            str_contains($label, 'mengubah') || str_contains($label, 'ubah') || str_contains($label, 'edit') => 'bg-[#DBEAFE] text-[#2563EB]',
            str_contains($label, 'logout') => 'bg-[#FEE2E2] text-[#DC2626]',
            str_contains($label, 'laporan') || str_contains($label, 'melihat') => 'bg-[#FCE7F3] text-[#C026D3]',
            str_contains($label, 'menghapus') || str_contains($label, 'hapus') || str_contains($label, 'tolak') => 'bg-[#FFE4E6] text-[#E11D48]',
            default => 'bg-[#F1F5F9] text-[#475569]'
        };
    }

    /**
     * Helper: Catat log aktivitas baru.
     */
    public static function catat(string $tindakan, ?string $detail = null, ?int $penggunaId = null): self
    {
        return self::create([
            'pengguna_id' => $penggunaId ?? auth()->id(),
            'tindakan' => $tindakan,
            'detail_kegiatan' => $detail,
            'alamat_ip' => request()->ip(),
        ]);
    }
}
