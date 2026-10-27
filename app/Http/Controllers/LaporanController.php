<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $query = Peminjaman::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni);

        $statistikBulanIni = [
            'total' => $query->count(),
            'selesai' => (clone $query)->where('status_pengajuan', 'disetujui')->has('pengembalian')->count(),
            'berjalan' => (clone $query)->where('status_pengajuan', 'disetujui')->doesntHave('pengembalian')->count(),
            'ditolak' => (clone $query)->where('status_pengajuan', 'ditolak')->count(),
        ];

        $fasilitasTerpopuler = Fasilitas::withCount(['peminjaman' => function ($query) use ($bulanIni, $tahunIni) {
            $query->whereMonth('created_at', $bulanIni)
                  ->whereYear('created_at', $tahunIni);
        }])
        ->orderByDesc('peminjaman_count')
        ->take(5)
        ->get();

        return view('admin.laporan.index', compact('statistikBulanIni', 'fasilitasTerpopuler'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_fasilitas' => 'nullable|in:ruangan,peralatan',
        ]);

        $query = Peminjaman::with(['pengguna', 'fasilitas.kategori', 'pengembalian'])
            ->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai]);

        if ($request->jenis_fasilitas) {
            $query->whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', $request->jenis_fasilitas));
        }

        $peminjaman = $query->orderBy('tanggal_mulai')->get();

        $data = [
            'peminjaman' => $peminjaman,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jenis_fasilitas' => $request->jenis_fasilitas,
            'tanggal_cetak' => now()->format('d/m/Y H:i'),
        ];

        // Jika DomPDF tersedia, generate PDF
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = Pdf::loadView('laporan.pdf', $data)
                ->setPaper('a4', 'landscape');

            return $pdf->download('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
        }

        // Fallback: tampilkan sebagai HTML
        return view('laporan.pdf', $data);
    }
}
