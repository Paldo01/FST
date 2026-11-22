<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class NotifikasiController extends Controller
{
    public function baca(string $notification): RedirectResponse
    {
        $notifikasi = auth()->user()->notifications()->whereKey($notification)->firstOrFail();
        $url = $notifikasi->data['url'] ?? route('dashboard', absolute: false);

        // Hapus notifikasi setelah diklik agar langsung hilang dari daftar
        $notifikasi->delete();

        if (!is_string($url) || !str_starts_with($url, '/') || str_starts_with($url, '//')) {
            $url = route('dashboard', absolute: false);
        }

        return redirect($url);
    }

    public function bacaSemua(): RedirectResponse
    {
        // Hapus semua notifikasi milik user
        auth()->user()->notifications()->delete();

        return back();
    }
}