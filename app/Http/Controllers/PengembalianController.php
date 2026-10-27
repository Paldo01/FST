<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::whereIn('status_pengajuan', ['disetujui', 'selesai'])
            ->with(['pengguna', 'fasilitas.kategori', 'pengembalian.admin']);

        // Filter Pencarian (nama peminjam, nomor identitas, atau nama fasilitas)
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->whereHas('pengguna', function ($qu) use ($cari) {
                    $qu->where('nama_lengkap', 'like', "%{$cari}%")
                       ->orWhere('nomor_identitas', 'like', "%{$cari}%");
                })->orWhereHas('fasilitas', function ($qf) use ($cari) {
                    $qf->where('nama_fasilitas', 'like', "%{$cari}%");
                });
            });
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_mulai', $request->tanggal);
        }

        // Urutkan: Yang belum dikembalikan otomatis berada di posisi paling atas
        $daftarPengembalian = $query
            ->orderByRaw("CASE WHEN status_pengajuan = 'disetujui' AND NOT EXISTS (SELECT 1 FROM pengembalian WHERE pengembalian.peminjaman_id = peminjaman.id) THEN 0 ELSE 1 END ASC")
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('daftarPengembalian'));
    }

    public function store(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'kondisi_akhir' => 'required|in:baik,rusak,hilang',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'admin_id' => auth()->id(),
            'tanggal_pengembalian' => now(),
            'kondisi_akhir' => $validated['kondisi_akhir'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
        ]);

        // Update status peminjaman menjadi selesai
        $peminjaman->update(['status_pengajuan' => 'selesai']);

        // Update kondisi fasilitas jika rusak/hilang
        if ($validated['kondisi_akhir'] !== 'baik') {
            $peminjaman->fasilitas->update(['status' => 'perbaikan']);
        } else {
            $peminjaman->fasilitas->update(['status' => 'tersedia']);
        }

        LogAktivitas::catat(
            'proses_pengembalian',
            'Memproses pengembalian peminjaman #' . $peminjaman->id . ' - Kondisi: ' . $validated['kondisi_akhir']
        );

        return back()->with('pesan_sukses', 'Pengembalian berhasil diproses!');
    }
}
