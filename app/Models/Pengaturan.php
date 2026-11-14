<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pengaturan extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Dapatkan nilai pengaturan berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if ($setting && $setting->value !== null && $setting->value !== '') {
            return $setting->value;
        }
        return $default;
    }

    /**
     * Simpan atau perbarui nilai pengaturan.
     */
    public static function set(string $key, mixed $value): static
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();
        return $setting;
    }

    /**
     * Dapatkan URL Logo Website (dengan fallback ke logo default).
     */
    public static function logoUrl(): string
    {
        $logoPath = static::get('logo');
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            return Storage::url($logoPath);
        }
        return asset('images/logo-fst.png');
    }

    /**
     * Dapatkan URL Favicon Website.
     */
    public static function faviconUrl(): string
    {
        $faviconPath = static::get('favicon');
        if ($faviconPath && Storage::disk('public')->exists($faviconPath)) {
            return Storage::url($faviconPath);
        }
        return static::logoUrl();
    }
}

