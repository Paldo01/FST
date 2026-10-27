<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index()
    {
        $peminjamanAktif = Peminjaman::where('status_pengajuan', 'disetujui')
            ->whereDoesntHave('pengembalian')
            ->with(['pengguna', 'fasilitas'])
            ->latest()
            ->get();

        $riwayatPengembalian = Pengembalian::with(['peminjaman.pengguna', 'peminjaman.fasilitas', 'admin'])
            ->latest()
            ->paginate(15);

        return view('admin.pengembalian.index', compact('peminjamanAktif', 'riwayatPengembalian'));
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
