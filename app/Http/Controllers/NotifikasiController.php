<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class NotifikasiController extends Controller
{
    public function baca(string $notification): RedirectResponse
    {
        $notifikasi = auth()->user()->notifications()->whereKey($notification)->firstOrFail();
        $notifikasi->markAsRead();

        $url = $notifikasi->data['url'] ?? route('dashboard', absolute: false);

        if (!is_string($url) || !str_starts_with($url, '/') || str_starts_with($url, '//')) {
            $url = route('dashboard', absolute: false);
        }

        return redirect($url);
    }

    public function bacaSemua(): RedirectResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}