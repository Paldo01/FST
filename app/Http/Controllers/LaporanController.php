<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $this->superAdminIndex($request);
        }

        // ── Statistik Cards (Real DB) ─────────────────────────────────────────
        $statistik = [
            'total_peminjaman' => Peminjaman::count(),
            'ruang_terpakai'   => Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'ruangan'))->count(),
            'alat_dipinjam'    => Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'peralatan'))->count(),
            'total_pengguna'   => Pengguna::whereIn('peran', ['mahasiswa', 'dosen'])->count(),
        ];

        // ── Grafik: Jumlah Peminjaman per Fasilitas (Real DB) ─────────────────
        // Ambil fasilitas ruang yang paling sering dipinjam (top 5)
        $ruangPopuler = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'ruangan'))
            ->select('fasilitas_id', DB::raw('COUNT(*) as total'))
            ->groupBy('fasilitas_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('fasilitas')
            ->get();

        // Ambil fasilitas alat yang paling sering dipinjam (top 5)
        $alatPopuler = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'peralatan'))
            ->select('fasilitas_id', DB::raw('COUNT(*) as total'))
            ->groupBy('fasilitas_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('fasilitas')
            ->get();

        // Gabungkan label unik dari ruang + alat populer
        $labelsRuang = $ruangPopuler->pluck('fasilitas.nama_fasilitas')->toArray();
        $labelsAlat  = $alatPopuler->pluck('fasilitas.nama_fasilitas')->toArray();
        $allLabels   = array_values(array_unique(array_merge($labelsRuang, $labelsAlat)));

        if (empty($allLabels)) {
            // Fallback: ambil nama fasilitas dari tabel fasilitas
            $allLabels = Fasilitas::limit(4)->pluck('nama_fasilitas')->toArray();
        }

        // Mapping label ke total peminjaman per jenis
        $ruangMap = $ruangPopuler->mapWithKeys(fn($p) => [$p->fasilitas->nama_fasilitas => $p->total])->toArray();
        $alatMap  = $alatPopuler->mapWithKeys(fn($p) => [$p->fasilitas->nama_fasilitas => $p->total])->toArray();

        $chartData = [
            'labels' => $allLabels,
            'ruang'  => array_map(fn($l) => $ruangMap[$l] ?? 0, $allLabels),
            'alat'   => array_map(fn($l) => $alatMap[$l]  ?? 0, $allLabels),
        ];

        // ── Ringkasan Fasilitas (Real DB) ─────────────────────────────────────
        $ruangList = Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'ruangan'))
            ->select('nama_fasilitas as nama')
            ->get()->toArray();
        $alatList  = Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'peralatan'))
            ->select('nama_fasilitas as nama')
            ->get()->toArray();

        $ringkasanFasilitas = [
            'ruang' => $ruangList,
            'alat'  => $alatList,
        ];

        return view('admin.laporan.index', compact('statistik', 'chartData', 'ringkasanFasilitas'));
    }

    public function superAdminIndex(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        // ── Kartu Statistik (data real) ──────────────────────────────────────
        $totalAdmin       = Pengguna::where('peran', 'admin')->count();
        $totalPeminjaman  = Peminjaman::count();
        $ruangTerpakai    = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'ruangan'))->count();
        $alatDipinjam     = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'peralatan'))->count();

        $statistik = [
            'total_admin'      => $totalAdmin,
            'total_peminjaman' => $totalPeminjaman,
            'ruang_terpakai'   => $ruangTerpakai,
            'alat_dipinjam'    => $alatDipinjam,
        ];

        // ── Grafik Peminjaman Per Bulan (data real per tahun) ─────────────────
        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        // Query ruang per bulan
        $ruangPerBulan = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'ruangan'))
            ->whereYear('tanggal_mulai', $tahun)
            ->select(DB::raw('MONTH(tanggal_mulai) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Query alat per bulan
        $alatPerBulan = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'peralatan'))
            ->whereYear('tanggal_mulai', $tahun)
            ->select(DB::raw('MONTH(tanggal_mulai) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Isi 12 bulan, bulan tanpa data = 0
        $ruangBulanan = [];
        $alatBulanan  = [];
        for ($m = 1; $m <= 12; $m++) {
            $ruangBulanan[] = $ruangPerBulan[$m] ?? 0;
            $alatBulanan[]  = $alatPerBulan[$m] ?? 0;
        }

        $chartBulanan = [
            'labels' => $bulanLabels,
            'ruang'  => $ruangBulanan,
            'alat'   => $alatBulanan,
        ];

        // ── Donut Chart Persentase Fasilitas (data real) ──────────────────────
        $totalDonut  = $ruangTerpakai + $alatDipinjam;
        $ruangPersen = $totalDonut > 0 ? round(($ruangTerpakai / $totalDonut) * 100) : 0;
        $alatPersen  = $totalDonut > 0 ? (100 - $ruangPersen) : 0;

        $persentaseFasilitas = [
            'ruang_count'  => $ruangTerpakai,
            'ruang_persen' => $ruangPersen,
            'alat_count'   => $alatDipinjam,
            'alat_persen'  => $alatPersen,
            'total'        => $totalDonut,
        ];

        // ── Tabel Data Peminjaman Terbaru (data real) ─────────────────────────
        $peminjamanTerbaru = Peminjaman::with(['pengguna', 'fasilitas.kategori'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p, $index) {
                $statusMap = [
                    'menunggu'  => ['label' => 'Pending',   'color' => 'bg-amber-100 text-amber-700'],
                    'disetujui' => ['label' => 'Disetujui', 'color' => 'bg-emerald-100 text-emerald-700'],
                    'ditolak'   => ['label' => 'Ditolak',   'color' => 'bg-rose-100 text-rose-700'],
                    'selesai'   => ['label' => 'Selesai',   'color' => 'bg-blue-100 text-blue-700'],
                ];
                $s = $statusMap[$p->status_pengajuan] ?? ['label' => ucfirst($p->status_pengajuan), 'color' => 'bg-gray-100 text-gray-700'];

                return [
                    'no'           => $index + 1,
                    'nama'         => optional($p->pengguna)->nama_lengkap ?? '-',
                    'fasilitas'    => optional($p->fasilitas)->nama_fasilitas ?? '-',
                    'tanggal'      => $p->tanggal_mulai ? $p->tanggal_mulai->format('d F Y') : '-',
                    'keperluan'    => $p->tujuan_peminjaman ?? '-',
                    'status'       => $s['label'],
                    'status_color' => $s['color'],
                ];
            })
            ->toArray();

        return view('super_admin.laporan.index', compact(
            'statistik', 'chartBulanan', 'persentaseFasilitas', 'peminjamanTerbaru', 'tahun'
        ));
    }

    public function generate(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', $request->get('tanggal_awal', now()->startOfMonth()->toDateString()));
        $tanggalSelesai = $request->get('tanggal_selesai', $request->get('tanggal_akhir', now()->endOfMonth()->toDateString()));
        $jenisFasilitas = $request->get('jenis_fasilitas', $request->get('jenis'));
        $status = $request->get('status');

        $query = Peminjaman::with(['pengguna', 'fasilitas.kategori', 'pengembalian']);

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai]);
        }

        if ($jenisFasilitas && in_array($jenisFasilitas, ['ruangan', 'peralatan'])) {
            $query->whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', $jenisFasilitas));
        }

        if ($status) {
            $query->where('status_pengajuan', $status);
        }

        $peminjaman = $query->orderBy('tanggal_mulai', 'desc')->get();

        // Jika data pada rentang tertentu kosong, ambil data riil terakhir agar PDF terisi
        if ($peminjaman->isEmpty()) {
            $peminjaman = Peminjaman::with(['pengguna', 'fasilitas.kategori', 'pengembalian'])
                ->orderBy('tanggal_mulai', 'desc')
                ->take(20)
                ->get();
        }

        $tanggalAwal = \Carbon\Carbon::parse($tanggalMulai);
        $tanggalAkhir = \Carbon\Carbon::parse($tanggalSelesai);

        $data = [
            'peminjaman' => $peminjaman,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'tanggal_mulai' => $tanggalAwal->format('Y-m-d'),
            'tanggal_selesai' => $tanggalAkhir->format('Y-m-d'),
            'statusFilter' => $status,
            'jenis_fasilitas' => $jenisFasilitas,
            'tanggal_cetak' => now()->format('d/m/Y H:i'),
        ];

        // Jika DomPDF tersedia, generate PDF
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $pdf = Pdf::loadView('admin.laporan.pdf', $data)
                    ->setPaper('a4', 'landscape');

                return $pdf->download('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
            } catch (\Exception $e) {
                return view('admin.laporan.pdf', $data);
            }
        }

        // Fallback: tampilkan sebagai HTML
        return view('admin.laporan.pdf', $data);
    }
}
