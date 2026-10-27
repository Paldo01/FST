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
     * Pengguna: Lihat daftar presensi & feedback (peminjaman selesai yang perlu diulas & riwayat ulasan).
     */
    public function indexPengguna()
    {
        $userId = auth()->id();

        $perluDiulas = Peminjaman::where('pengguna_id', $userId)
            ->where('status_pengajuan', 'selesai')
            ->whereDoesntHave('ulasan')
            ->with(['fasilitas'])
            ->latest()
            ->get();

        $riwayatUlasan = UlasanLayanan::where('pengguna_id', $userId)
            ->with(['peminjaman.fasilitas'])
            ->latest()
            ->paginate(10);

        return view('pengguna.ulasan.index', compact('perluDiulas', 'riwayatUlasan'));
    }

    /**
     * Super Admin: Lihat semua evaluasi feedback.
     */
    public function indexAdmin(Request $request)
    {
        $cari = $request->get('cari');
        $rentangTanggal = $request->get('rentang_tanggal');

        // Statistik Real dari Database
        $totalFeedback = UlasanLayanan::count();
        $avgRating = UlasanLayanan::avg('rating');
        $rataRataRating = $totalFeedback > 0 ? number_format($avgRating, 1, ',', '.') : '0';
        $feedbackTerbaru = UlasanLayanan::where('created_at', '>=', now()->subDays(30))->count();

        // Query Feedback Riil dari Database
        $query = UlasanLayanan::with(['pengguna', 'peminjaman.fasilitas'])->latest();

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('komentar', 'like', "%{$cari}%")
                  ->orWhereHas('pengguna', function ($qp) use ($cari) {
                      $qp->where('nama_lengkap', 'like', "%{$cari}%")
                         ->orWhere('jenis_pengguna', 'like', "%{$cari}%")
                         ->orWhere('peran', 'like', "%{$cari}%");
                  })
                  ->orWhereHas('peminjaman.fasilitas', function ($qf) use ($cari) {
                      $qf->where('nama_fasilitas', 'like', "%{$cari}%");
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
