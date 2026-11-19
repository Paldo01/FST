<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\KategoriFasilitas;
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
            'total_pengguna'   => Pengguna::count(),
        ];

        // ── Grafik: Peminjaman per Kategori (Real DB) ────────────────────────
        // Dapatkan semua nama kategori yang ada di sistem
        $allLabels = KategoriFasilitas::orderBy('nama_kategori')->pluck('nama_kategori')->toArray();

        // Helper untuk menghitung per periode
        $getChartByPeriod = function ($filterCallback = null) use ($allLabels) {
            $qRuang = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'ruangan'))
                ->join('fasilitas', 'peminjaman.fasilitas_id', '=', 'fasilitas.id')
                ->join('kategori_fasilitas', 'fasilitas.kategori_id', '=', 'kategori_fasilitas.id');
            if ($filterCallback) $filterCallback($qRuang);
            $ruangMap = $qRuang->select('kategori_fasilitas.nama_kategori as label', DB::raw('COUNT(peminjaman.id) as total'))
                ->groupBy('kategori_fasilitas.nama_kategori')
                ->pluck('total', 'label')->toArray();

            $qAlat = Peminjaman::whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', 'peralatan'))
                ->join('fasilitas', 'peminjaman.fasilitas_id', '=', 'fasilitas.id')
                ->join('kategori_fasilitas', 'fasilitas.kategori_id', '=', 'kategori_fasilitas.id');
            if ($filterCallback) $filterCallback($qAlat);
            $alatMap = $qAlat->select('kategori_fasilitas.nama_kategori as label', DB::raw('COUNT(peminjaman.id) as total'))
                ->groupBy('kategori_fasilitas.nama_kategori')
                ->pluck('total', 'label')->toArray();

            return [
                'ruang' => array_map(fn($l) => $ruangMap[$l] ?? 0, $allLabels),
                'alat'  => array_map(fn($l) => $alatMap[$l] ?? 0, $allLabels),
            ];
        };

        // Data real per filter periode
        $periodData = [
            'Semua'      => $getChartByPeriod(),
            'Bulan Ini'  => $getChartByPeriod(fn($q) => $q->whereMonth('peminjaman.tanggal_mulai', now()->month)->whereYear('peminjaman.tanggal_mulai', now()->year)),
            'Bulan Lalu' => $getChartByPeriod(fn($q) => $q->whereMonth('peminjaman.tanggal_mulai', now()->subMonth()->month)->whereYear('peminjaman.tanggal_mulai', now()->subMonth()->year)),
            'Tahun Ini'  => $getChartByPeriod(fn($q) => $q->whereYear('peminjaman.tanggal_mulai', now()->year)),
        ];

        // Saring kategori yang memiliki data (minimal > 0) agar grafik tidak dipenuhi angka 0
        $activeLabels = [];
        $activeRuang  = [];
        $activeAlat   = [];
        foreach ($allLabels as $idx => $label) {
            $totalSemua = $periodData['Semua']['ruang'][$idx] + $periodData['Semua']['alat'][$idx];
            if ($totalSemua > 0) {
                $activeLabels[] = $label;
                $activeRuang[]  = $periodData['Bulan Ini']['ruang'][$idx];
                $activeAlat[]   = $periodData['Bulan Ini']['alat'][$idx];
            }
        }

        // Jika tidak ada data yang > 0 di bulan ini, tampilkan semua kategori yang pernah dipinjam
        if (empty($activeLabels)) {
            $activeLabels = $allLabels;
            $activeRuang  = $periodData['Bulan Ini']['ruang'];
            $activeAlat   = $periodData['Bulan Ini']['alat'];
        }

        // Rekonstruksi periodData sesuai activeLabels
        $periodDataFiltered = [];
        foreach ($periodData as $periodKey => $pData) {
            $rFiltered = [];
            $aFiltered = [];
            foreach ($allLabels as $idx => $l) {
                if (in_array($l, $activeLabels)) {
                    $rFiltered[] = $pData['ruang'][$idx];
                    $aFiltered[] = $pData['alat'][$idx];
                }
            }
            $periodDataFiltered[$periodKey] = [
                'ruang' => $rFiltered,
                'alat'  => $aFiltered,
            ];
        }

        $chartData = [
            'labels' => $activeLabels,
            'ruang'  => $periodDataFiltered['Bulan Ini']['ruang'],
            'alat'   => $periodDataFiltered['Bulan Ini']['alat'],
        ];

        // ── Ringkasan Fasilitas: per Kategori (Real DB) ───────────────────────
        $ruangList = KategoriFasilitas::where('jenis', 'ruangan')
            ->withCount('fasilitas')
            ->get()
            ->map(fn($k) => ['nama' => $k->nama_kategori, 'jumlah' => $k->fasilitas_count])
            ->toArray();

        $alatList = KategoriFasilitas::where('jenis', 'peralatan')
            ->withCount('fasilitas')
            ->get()
            ->map(fn($k) => ['nama' => $k->nama_kategori, 'jumlah' => $k->fasilitas_count])
            ->toArray();

        $ringkasanFasilitas = [
            'ruang' => $ruangList,
            'alat'  => $alatList,
        ];

        return view('admin.laporan.index', [
            'statistik'          => $statistik,
            'chartData'          => $chartData,
            'periodData'         => $periodDataFiltered,
            'ringkasanFasilitas' => $ringkasanFasilitas,
        ]);
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
                    'kategori'     => optional(optional($p->fasilitas)->kategori)->nama_kategori ?? '-',
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
