<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\UlasanLayanan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match ($user->peran) {
            'super_admin' => $this->dashboardSuperAdmin(),
            'admin' => $this->dashboardAdmin(),
            default => $this->dashboardPengguna(),
        };
    }

    private function dashboardPengguna()
    {
        $user = auth()->user();

        $peminjamanAktif = Peminjaman::where('pengguna_id', $user->id)
            ->whereIn('status_pengajuan', ['menunggu', 'disetujui'])
            ->with('fasilitas')
            ->latest()
            ->get();

        $riwayatPeminjaman = Peminjaman::where('pengguna_id', $user->id)
            ->with(['fasilitas', 'ulasan'])
            ->latest()
            ->paginate(10);

        $statistik = [
            'total' => Peminjaman::where('pengguna_id', $user->id)->count(),
            'menunggu' => Peminjaman::where('pengguna_id', $user->id)->where('status_pengajuan', 'menunggu')->count(),
            'disetujui' => Peminjaman::where('pengguna_id', $user->id)->where('status_pengajuan', 'disetujui')->count(),
            'selesai' => Peminjaman::where('pengguna_id', $user->id)->where('status_pengajuan', 'selesai')->count(),
        ];

        return view('pengguna.dashboard', compact('peminjamanAktif', 'riwayatPeminjaman', 'statistik'));
    }

    private function dashboardAdmin()
    {
        $menungguVerifikasi = Peminjaman::where('status_pengajuan', 'menunggu')
            ->with(['pengguna', 'fasilitas'])
            ->latest()
            ->get();

        $peminjamanBulanIni = Peminjaman::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Data untuk grafik bulanan (6 bulan terakhir)
        $grafikBulanan = Peminjaman::select(
                DB::raw("MONTH(created_at) as bulan"),
                DB::raw("YEAR(created_at) as tahun"),
                DB::raw("COUNT(*) as total")
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $statistik = [
            'total_fasilitas' => Fasilitas::count(),
            'fasilitas_tersedia' => Fasilitas::where('status', 'tersedia')->count(),
            'peminjaman_bulan_ini' => $peminjamanBulanIni,
            'menunggu_verifikasi' => $menungguVerifikasi->count(),
            'pengembalian_pending' => Peminjaman::where('status_pengajuan', 'disetujui')
                ->whereDoesntHave('pengembalian')
                ->count(),
        ];

        return view('admin.dashboard', compact('menungguVerifikasi', 'statistik', 'grafikBulanan'));
    }

    private function dashboardSuperAdmin()
    {
        $statistik = [
            'total_pengguna' => \App\Models\Pengguna::count(),
            'total_admin' => \App\Models\Pengguna::where('peran', 'admin')->count(),
            'total_fasilitas' => Fasilitas::count(),
            'total_peminjaman' => Peminjaman::count(),
            'peminjaman_bulan_ini' => Peminjaman::whereMonth('created_at', now()->month)->count(),
            'rata_rata_rating' => round(UlasanLayanan::avg('rating') ?? 0, 1),
        ];

        $grafikBulanan = Peminjaman::select(
                DB::raw("MONTH(created_at) as bulan"),
                DB::raw("YEAR(created_at) as tahun"),
                DB::raw("COUNT(*) as total")
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $ulasanTerbaru = UlasanLayanan::with(['pengguna', 'peminjaman.fasilitas'])
            ->latest()
            ->limit(5)
            ->get();

        return view('super_admin.dashboard', compact('statistik', 'grafikBulanan', 'ulasanTerbaru'));
    }
}
