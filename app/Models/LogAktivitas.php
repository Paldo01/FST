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
