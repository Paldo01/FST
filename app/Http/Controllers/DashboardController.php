<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Pengguna;
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
            ->with(['pengguna', 'fasilitas.kategori'])
            ->latest()
            ->get();

        $pengajuanTerbaru = Peminjaman::with(['pengguna', 'fasilitas.kategori'])
            ->latest()
            ->limit(10)
            ->get();

        $peminjamanBulanIni = Peminjaman::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Data untuk grafik bulanan (6 bulan terakhir sinkron dengan tanggal peminjaman)
        $grafikBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = now()->subMonths($i);
            $b = (int) $dt->month;
            $y = (int) $dt->year;
            $total = Peminjaman::whereYear('tanggal_mulai', $y)
                ->whereMonth('tanggal_mulai', $b)
                ->count();
            $grafikBulanan[] = [
                'bulan' => $b,
                'tahun' => $y,
                'total' => $total,
            ];
        }

        $statistik = [
            'total_fasilitas' => Fasilitas::count(),
            'fasilitas_tersedia' => Fasilitas::where('status', 'tersedia')->count(),
            'peminjaman_bulan_ini' => $peminjamanBulanIni,
            'menunggu_verifikasi' => Peminjaman::where('status_pengajuan', 'menunggu')->count(),
            'disetujui' => Peminjaman::where('status_pengajuan', 'disetujui')->count(),
            'ditolak' => Peminjaman::where('status_pengajuan', 'ditolak')->count(),
            'total_peminjaman' => Peminjaman::count(),
            'pengembalian_pending' => Peminjaman::where('status_pengajuan', 'disetujui')
                ->whereDoesntHave('pengembalian')
                ->count(),
        ];

        return view('admin.dashboard', compact('menungguVerifikasi', 'pengajuanTerbaru', 'statistik', 'grafikBulanan'));
    }

    private function dashboardSuperAdmin()
    {
        // ── Statistik Utama (Real DB) ──────────────────────────────────────
        $statistik = [
            'total_admin'      => Pengguna::where('peran', 'admin')->count(),
            'total_peminjaman' => Peminjaman::count(),
            'total_ruangan'    => Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'ruangan'))->count(),
            'total_peralatan'  => Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'peralatan'))->count(),
            'total_pengguna'   => Pengguna::count(),
        ];

        // ── Status Peminjaman (Real DB) ────────────────────────────────────
        $statusCounts = [
            'pending'   => Peminjaman::where('status_pengajuan', 'menunggu')->count(),
            'disetujui' => Peminjaman::where('status_pengajuan', 'disetujui')->count(),
            'ditolak'   => Peminjaman::where('status_pengajuan', 'ditolak')->count(),
            'selesai'   => Peminjaman::where('status_pengajuan', 'selesai')->count(),
        ];

        // ── Aktivitas Terbaru dari LogAktivitas (Real DB) ──────────────────
        $ikonMap = [
            'setujui'    => ['icon' => 'check',      'bg' => 'bg-[#DCFCE7]', 'color' => 'text-[#16A34A]'],
            'tolak'      => ['icon' => 'x-circle',   'bg' => 'bg-[#FEE2E2]', 'color' => 'text-[#E11D48]'],
            'tambah'     => ['icon' => 'plus-circle', 'bg' => 'bg-[#DBEAFE]', 'color' => 'text-[#2563EB]'],
            'edit'       => ['icon' => 'edit-3',      'bg' => 'bg-[#FEF3C7]', 'color' => 'text-[#D97706]'],
            'hapus'      => ['icon' => 'trash-2',     'bg' => 'bg-[#FEE2E2]', 'color' => 'text-[#E11D48]'],
            'login'      => ['icon' => 'log-in',      'bg' => 'bg-[#F3E8FF]', 'color' => 'text-[#9333EA]'],
            'daftar'     => ['icon' => 'user-plus',   'bg' => 'bg-[#DCFCE7]', 'color' => 'text-[#16A34A]'],
            'default'    => ['icon' => 'activity',    'bg' => 'bg-[#F1F5F9]', 'color' => 'text-[#64748B]'],
        ];

        $logsRaw = LogAktivitas::with('pengguna')
            ->latest()
            ->limit(5)
            ->get();

        $aktivitasTerbaru = $logsRaw->map(function ($log) use ($ikonMap) {
            $aksi = strtolower($log->aksi ?? '');
            $key = 'default';
            foreach (array_keys($ikonMap) as $k) {
                if ($k !== 'default' && str_contains($aksi, $k)) {
                    $key = $k;
                    break;
                }
            }
            $ikon = $ikonMap[$key];
            $nama = $log->pengguna?->nama_lengkap ?? 'Sistem';
            return [
                'tipe'       => $key,
                'judul'      => $nama . ' — ' . ($log->deskripsi ?? $log->aksi),
                'waktu'      => $log->created_at->translatedFormat('d M Y, H:i'),
                'icon'       => $ikon['icon'],
                'bg_color'   => $ikon['bg'],
                'icon_color' => $ikon['color'],
            ];
        })->toArray();

        // ── Audit Log Terbaru ──────────────────────────────────────────────
        $latestLog = LogAktivitas::latest()->first();
        $auditLogTerbaru = $latestLog
            ? $latestLog->created_at->translatedFormat('d M Y, H:i')
            : 'Belum ada aktivitas';

        // ── Ringkasan Sistem (Real DB) ─────────────────────────────────────
        $ringkasanSistem = [
            'admin_aktif'        => Pengguna::where('peran', 'admin')->count(),
            'total_feedback'     => UlasanLayanan::count(),
            'audit_log_terbaru'  => $auditLogTerbaru,
        ];

        return view('super_admin.dashboard', compact('statistik', 'statusCounts', 'aktivitasTerbaru', 'ringkasanSistem'));
    }
}
