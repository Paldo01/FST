<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\UlasanLayanan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        UlasanLayanan::create([
            'peminjaman_id' => null,
            'pengguna_id' => auth()->id(),
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'] ?? null,
        ]);

        LogAktivitas::catat('beri_feedback', 'Memberikan feedback untuk sistem');

        return redirect()->route('ulasan.pengguna')
            ->with('pesan_sukses', 'Terima kasih atas ulasan Anda!');
    }

    public function indexPengguna()
    {
        $riwayatUlasan = UlasanLayanan::where('pengguna_id', auth()->id())
            ->whereNull('peminjaman_id')
            ->latest()
            ->paginate(10);

        return view('pengguna.ulasan.index', compact('riwayatUlasan'));
    }

    /**
     * Super Admin: Lihat semua evaluasi feedback.
     */
    public function indexAdmin(Request $request)
    {
        $cari = $request->get('cari');
        $rentangTanggal = $request->get('rentang_tanggal');

        // Statistik Real dari Database
        $feedbackQuery = UlasanLayanan::whereNull('peminjaman_id');
        $totalFeedback = (clone $feedbackQuery)->count();
        $avgRating = (clone $feedbackQuery)->avg('rating');
        $rataRataRating = $totalFeedback > 0 ? number_format($avgRating, 1, ',', '.') : '0';
        $feedbackTerbaru = (clone $feedbackQuery)->where('created_at', '>=', now()->subDays(30))->count();

        // Query Feedback Riil dari Database
        $query = UlasanLayanan::whereNull('peminjaman_id')->with('pengguna')->latest();

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('komentar', 'like', "%{$cari}%")
                  ->orWhereHas('pengguna', function ($qp) use ($cari) {
                      $qp->where('nama_lengkap', 'like', "%{$cari}%")
                         ->orWhere('jenis_pengguna', 'like', "%{$cari}%")
                         ->orWhere('peran', 'like', "%{$cari}%");
                  });
            });
        }

        if ($rentangTanggal) {
            if (str_contains($rentangTanggal, ' to ')) {
                [$start, $end] = explode(' to ', $rentangTanggal, 2);
                $query->whereDate('created_at', '>=', trim($start))
                      ->whereDate('created_at', '<=', trim($end));
            } elseif (str_contains($rentangTanggal, ' - ')) {
                [$start, $end] = explode(' - ', $rentangTanggal, 2);
                $query->whereDate('created_at', '>=', trim($start))
                      ->whereDate('created_at', '<=', trim($end));
            } else {
                $query->whereDate('created_at', trim($rentangTanggal));
            }
        }

        $feedbacks = $query->paginate(8)->withQueryString();

        return view('super_admin.ulasan.index', compact(
            'rataRataRating',
            'totalFeedback',
            'feedbackTerbaru',
            'feedbacks',
            'cari',
            'rentangTanggal'
        ));
    }
}
