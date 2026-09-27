<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    /**
     * Handle an incoming request.
     * Usage: middleware('peran:admin,super_admin')
     */
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        if (!auth()->check()) {
            return redirect()->route('masuk')->with('pesan_error', 'Silakan masuk terlebih dahulu.');
        }

        $penggunaSaatIni = auth()->user();

        if (!in_array($penggunaSaatIni->peran, $peranDiizinkan)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
