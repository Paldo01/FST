<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\UlasanLayanan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function create(Peminjaman $peminjaman)
    {
        // Hanya pemilik peminjaman & status selesai
        if ($peminjaman->pengguna_id !== auth()->id() || $peminjaman->status_pengajuan !== 'selesai') {
            abort(403);
        }

        // Cek apakah sudah memberi ulasan
        if ($peminjaman->ulasan) {
            return back()->with('pesan_error', 'Anda sudah memberikan ulasan untuk peminjaman ini.');
        }

        $peminjaman->load('fasilitas');

        return view('pengguna.ulasan.buat', compact('peminjaman'));
    }

    public function store(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->pengguna_id !== auth()->id() || $peminjaman->status_pengajuan !== 'selesai') {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        UlasanLayanan::create([
            'peminjaman_id' => $peminjaman->id,
            'pengguna_id' => auth()->id(),
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'] ?? null,
        ]);

        LogAktivitas::catat('beri_ulasan', 'Memberikan ulasan untuk peminjaman #' . $peminjaman->id);

        return redirect()->route('peminjaman.index')
            ->with('pesan_sukses', 'Terima kasih atas ulasan Anda!');
    }

    /**
     * Super Admin: Lihat semua ulasan.
     */
    public function indexAdmin()
    {
        $ulasan = UlasanLayanan::with(['pengguna', 'peminjaman.fasilitas'])
            ->latest()
            ->paginate(15);

        $rataRating = round(UlasanLayanan::avg('rating') ?? 0, 1);
        $totalUlasan = UlasanLayanan::count();

        return view('super_admin.ulasan.index', compact('ulasan', 'rataRating', 'totalUlasan'));
    }
}
