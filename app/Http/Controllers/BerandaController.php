<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\KategoriFasilitas;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        $kategoriList = KategoriFasilitas::withCount('fasilitas')->get();
        $fasilitasTersedia = Fasilitas::where('status', 'tersedia')->count();
        $totalFasilitas = Fasilitas::count();

        return view('beranda', compact('kategoriList', 'fasilitasTersedia', 'totalFasilitas'));
    }

    public function katalogRuang()
    {
        $fasilitas = Fasilitas::with('kategori')
            ->whereHas('kategori', fn($q) => $q->where('jenis', 'ruangan'))
            ->orderBy('status')
            ->get();

        $dipinjamIds = Peminjaman::where('status_pengajuan', 'disetujui')->pluck('fasilitas_id')->toArray();

        return view('pengguna.katalog.ruang', compact('fasilitas', 'dipinjamIds'));
    }

    public function katalogAlat()
    {
        $fasilitas = Fasilitas::with('kategori')
            ->whereHas('kategori', fn($q) => $q->where('jenis', 'peralatan'))
            ->orderBy('status')
            ->get();

        $dipinjamIds = Peminjaman::where('status_pengajuan', 'disetujui')->pluck('fasilitas_id')->toArray();

        return view('pengguna.katalog.alat', compact('fasilitas', 'dipinjamIds'));
    }

    /**
     * API: Data ketersediaan fasilitas untuk kalender interaktif.
     */
    public function ketersediaan(Request $request)
    {
        $peminjaman = Peminjaman::with(['fasilitas', 'pengguna'])
            ->whereIn('status_pengajuan', ['disetujui', 'menunggu'])
            ->when($request->start, fn($q) => $q->where('tanggal_selesai', '>=', $request->start))
            ->when($request->end, fn($q) => $q->where('tanggal_mulai', '<=', $request->end))
            ->get();

        $events = $peminjaman->map(function ($p) {
            $warna = match ($p->status_pengajuan) {
                'disetujui' => '#dc2626',
                'menunggu' => '#f59e0b',
                default => '#6b7280',
            };

            return [
                'id' => $p->id,
                'title' => $p->fasilitas->nama_fasilitas,
                'start' => $p->tanggal_mulai->toIso8601String(),
                'end' => $p->tanggal_selesai->toIso8601String(),
                'color' => $warna,
                'extendedProps' => [
                    'status' => $p->status_pengajuan,
                    'peminjam' => $p->pengguna->nama_lengkap,
                    'tujuan' => $p->tujuan_peminjaman,
                    'fasilitas' => $p->fasilitas->nama_fasilitas,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * API: Pencarian fasilitas (live search).
     */
    public function cariFasilitas(Request $request)
    {
        $fasilitas = Fasilitas::with('kategori')
            ->when($request->q, function ($query) use ($request) {
                $query->where('nama_fasilitas', 'like', "%{$request->q}%")
                    ->orWhere('kode_fasilitas', 'like', "%{$request->q}%");
            })
            ->when($request->jenis, function ($query) use ($request) {
                $query->whereHas('kategori', fn($q) => $q->where('jenis', $request->jenis));
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->limit(20)
            ->get();

        return response()->json($fasilitas);
    }
}
