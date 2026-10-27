<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('peminjaman.status');
    }

    /**
     * Menampilkan status peminjaman yang sedang aktif / dalam proses.
     */
    public function status(Request $request)
    {
        $userId = auth()->id();

        // Ambil peminjaman aktif (menunggu persetujuan atau disetujui/sedang berjalan)
        $peminjamanAktif = Peminjaman::where('pengguna_id', $userId)
            ->with(['fasilitas.kategori', 'pengembalian'])
            ->whereIn('status_pengajuan', ['menunggu', 'disetujui'])
            ->latest('created_at')
            ->get();

        $countMenunggu = $peminjamanAktif->where('status_pengajuan', 'menunggu')->count();
        $countDisetujui = $peminjamanAktif->where('status_pengajuan', 'disetujui')->count();

        $countRiwayat = Peminjaman::where('pengguna_id', $userId)
            ->whereIn('status_pengajuan', ['selesai', 'ditolak', 'dibatalkan'])
            ->count();

        return view('pengguna.peminjaman.status', compact(
            'peminjamanAktif',
            'countMenunggu',
            'countDisetujui',
            'countRiwayat'
        ));
    }

    /**
     * Menampilkan riwayat peminjaman yang sudah selesai atau ditolak.
     */
    public function riwayat(Request $request)
    {
        $userId = auth()->id();
        $cari = $request->input('cari');
        $status = $request->input('status');
        $bulan = $request->input('bulan');

        $query = Peminjaman::where('pengguna_id', $userId)
            ->with(['fasilitas.kategori', 'pengembalian', 'ulasan'])
            ->whereIn('status_pengajuan', ['selesai', 'ditolak', 'dibatalkan']);

        if ($cari) {
            $query->where(function($q) use ($cari) {
                $q->where('kode_peminjaman', 'like', "%{$cari}%")
                  ->orWhere('tujuan_peminjaman', 'like', "%{$cari}%")
                  ->orWhereHas('fasilitas', function($qf) use ($cari) {
                      $qf->where('nama_fasilitas', 'like', "%{$cari}%");
                  });
            });
        }

        if ($status && in_array($status, ['selesai', 'ditolak', 'dibatalkan'])) {
            $query->where('status_pengajuan', $status);
        }

        if ($bulan) {
            $carbonBulan = \Carbon\Carbon::parse($bulan);
            $query->whereYear('tanggal_mulai', $carbonBulan->year)
                  ->whereMonth('tanggal_mulai', $carbonBulan->month);
        }

        $riwayatList = $query->latest('tanggal_mulai')->paginate(10)->withQueryString();

        $countAktif = Peminjaman::where('pengguna_id', $userId)
            ->whereIn('status_pengajuan', ['menunggu', 'disetujui'])
            ->count();

        $countSelesai = Peminjaman::where('pengguna_id', $userId)->where('status_pengajuan', 'selesai')->count();
        $countDitolak = Peminjaman::where('pengguna_id', $userId)->whereIn('status_pengajuan', ['ditolak', 'dibatalkan'])->count();

        return view('pengguna.peminjaman.riwayat', compact(
            'riwayatList',
            'countAktif',
            'countSelesai',
            'countDitolak',
            'cari',
            'status',
            'bulan'
        ));
    }

    /**
     * Membatalkan pengajuan peminjaman oleh pengguna (jika masih berstatus menunggu).
     */
    public function batalkan(Peminjaman $peminjaman)
    {
        if ($peminjaman->pengguna_id !== auth()->id()) {
            abort(403);
        }

        if ($peminjaman->status_pengajuan !== 'menunggu') {
            return back()->withErrors(['pesan' => 'Hanya pengajuan dengan status menunggu yang dapat dibatalkan.']);
        }

        $peminjaman->status_pengajuan = 'dibatalkan';
        $peminjaman->alasan_penolakan = 'Dibatalkan oleh peminjam';
        $peminjaman->save();

        LogAktivitas::catat('batalkan_peminjaman', 'Membatalkan pengajuan peminjaman: ' . $peminjaman->kode_peminjaman);

        return redirect()->route('peminjaman.status')
            ->with('pesan_sukses', 'Pengajuan peminjaman berhasil dibatalkan.');
    }

    public function create(Request $request)
    {
        $fasilitasId = $request->input('fasilitas', $request->input('fasilitas_id'));
        if ($fasilitasId) {
            return redirect()->route('katalog.ruang.detail', [
                'fasilitas' => $fasilitasId,
                'ajukan' => 1,
            ]);
        }

        return redirect()->route('katalog.ruang')
            ->with('info', 'Silakan pilih ruangan di Katalog Ruang untuk mengajukan peminjaman.');
    }

    public function store(Request $request)
    {
        // Jika form mengirim tanggal_peminjaman, jam_mulai, dan jam_selesai terpisah
        if ($request->filled('tanggal_peminjaman') && $request->filled('jam_mulai') && $request->filled('jam_selesai')) {
            $tgl = $request->input('tanggal_peminjaman');
            $request->merge([
                'tanggal_mulai' => $tgl . ' ' . $request->input('jam_mulai') . ':00',
                'tanggal_selesai' => $tgl . ' ' . $request->input('jam_selesai') . ':00',
            ]);
        }

        $validated = $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tujuan_peminjaman' => 'required|string|max:1000',
            'jumlah_peserta' => 'nullable|integer|min:1',
            'berkas_persyaratan' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // Conflict checking: cek jadwal ganda
        $fasilitas = Fasilitas::with('kategori')->findOrFail($validated['fasilitas_id']);

        if ($fasilitas->kategori->jenis !== 'ruangan') {
            return back()->withErrors([
                'fasilitas_id' => 'Peminjaman peralatan/barang hanya dapat dilakukan langsung melalui Admin di loket.',
            ])->withInput();
        }

        if (!$fasilitas->tersediaPadaWaktu($validated['tanggal_mulai'], $validated['tanggal_selesai'])) {
            return back()->withErrors([
                'fasilitas_id' => 'Fasilitas ini sudah dipesan pada rentang waktu yang dipilih. Silakan pilih waktu lain.',
            ])->withInput();
        }

        if ($request->hasFile('berkas_persyaratan')) {
            $validated['berkas_persyaratan'] = $request->file('berkas_persyaratan')
                ->store('berkas-peminjaman', 'public');
        }

        $validated['pengguna_id'] = auth()->id();
        $validated['status_pengajuan'] = 'menunggu';

        $peminjaman = Peminjaman::create($validated);

        LogAktivitas::catat('ajukan_peminjaman', 'Mengajukan peminjaman fasilitas: ' . $fasilitas->nama_fasilitas);

        return redirect()->route('peminjaman.index')
            ->with('pesan_sukses', 'Pengajuan peminjaman berhasil dikirim! Menunggu persetujuan admin.');
    }

    public function show(Peminjaman $peminjaman)
    {
        // Pastikan hanya pemilik, admin, atau super_admin yang bisa melihat
        if (auth()->user()->isPengguna() && $peminjaman->pengguna_id !== auth()->id()) {
            abort(403);
        }

        $peminjaman->load(['fasilitas.kategori', 'pengguna', 'pengembalian.admin', 'ulasan']);

        return view('pengguna.peminjaman.detail', compact('peminjaman'));
    }

    /* ---- Admin: Verifikasi ---- */

    public function verifikasiIndex(Request $request)
    {
        $query = Peminjaman::with(['pengguna', 'fasilitas.kategori']);

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

        // Filter Status Pengajuan
        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_mulai', $request->tanggal);
        }

        $pengajuan = $query->latest('id')->paginate(10)->withQueryString();

        return view('admin.verifikasi.index', compact('pengajuan'));
    }

    public function setujui(Peminjaman $peminjaman)
    {
        $peminjaman->update(['status_pengajuan' => 'disetujui']);

        LogAktivitas::catat('setujui_peminjaman', 'Menyetujui peminjaman #' . $peminjaman->id . ' oleh ' . $peminjaman->pengguna->nama_lengkap);

        return back()->with('pesan_sukses', 'Peminjaman berhasil disetujui!');
    }

    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $peminjaman->update([
            'status_pengajuan' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        LogAktivitas::catat('tolak_peminjaman', 'Menolak peminjaman #' . $peminjaman->id . ': ' . $request->alasan_penolakan);

        return back()->with('pesan_sukses', 'Peminjaman telah ditolak.');
    }

    /* ---- Admin: Riwayat Semua Peminjaman ---- */

    public function adminRiwayat(Request $request)
    {
        $query = Peminjaman::with(['pengguna', 'fasilitas.kategori', 'pengembalian.admin']);

        // Filter pencarian nama peminjam / NIM / fasilitas / keperluan
        if ($request->filled('cari')) {
            $cari = trim($request->cari);
            $query->where(function ($q) use ($cari) {
                $q->whereHas('pengguna', function ($qu) use ($cari) {
                    $qu->where('nama_lengkap', 'like', "%{$cari}%")
                       ->orWhere('nomor_identitas', 'like', "%{$cari}%");
                })->orWhereHas('fasilitas', function ($qf) use ($cari) {
                    $qf->where('nama_fasilitas', 'like', "%{$cari}%");
                })->orWhere('tujuan_peminjaman', 'like', "%{$cari}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_mulai', $request->tanggal);
        }

        $riwayat = $query->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.riwayat.index', compact('riwayat'));
    }

    /**
     * API: Cek ketersediaan fasilitas pada rentang waktu tertentu.
     */
    public function cekKetersediaan(Request $request)
    {
        if ($request->filled('tanggal_peminjaman') && $request->filled('jam_mulai') && $request->filled('jam_selesai')) {
            $tgl = $request->input('tanggal_peminjaman');
            $request->merge([
                'tanggal_mulai' => $tgl . ' ' . $request->input('jam_mulai') . ':00',
                'tanggal_selesai' => $tgl . ' ' . $request->input('jam_selesai') . ':00',
            ]);
        }

        $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $fasilitas = Fasilitas::findOrFail($request->fasilitas_id);
        $tersedia = $fasilitas->tersediaPadaWaktu($request->tanggal_mulai, $request->tanggal_selesai);

        return response()->json([
            'tersedia' => $tersedia,
            'pesan' => $tersedia
                ? 'Fasilitas masih tersedia pada tanggal dan waktu yang dipilih'
                : 'Fasilitas sudah terpakai pada rentang tanggal dan waktu tersebut.',
        ]);
    }

    /* ---- Admin: Input Peminjaman Langsung ---- */

    public function adminCreate()
    {
        $fasilitas = Fasilitas::with('kategori')
            ->where('status', 'tersedia')
            ->get();
            
        $pengguna = \App\Models\Pengguna::whereNotNull('jenis_pengguna')->get();

        return view('admin.peminjaman.buat', compact('fasilitas', 'pengguna'));
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'pengguna_id' => 'required|exists:pengguna,id',
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tanggal_mulai' => 'required|date',
            'tujuan_peminjaman' => 'required|string|max:1000',
        ]);

        $fasilitas = Fasilitas::with('kategori')->findOrFail($validated['fasilitas_id']);

        // Cek apakah barang sedang dipinjam (belum dikembalikan)
        $sedangDipinjam = Peminjaman::where('fasilitas_id', $fasilitas->id)
            ->whereIn('status_pengajuan', ['disetujui'])
            ->exists();

        if ($sedangDipinjam) {
            return back()->withErrors([
                'fasilitas_id' => 'Alat ini sedang dipinjam oleh pengguna lain dan belum dikembalikan.',
            ])->withInput();
        }

        // Set tanggal selesai dummy agar database tidak error, 
        // durasi asli dihitung nanti saat alat dikembalikan
        $validated['tanggal_selesai'] = \Carbon\Carbon::parse($validated['tanggal_mulai'])->addYears(10);
        $validated['status_pengajuan'] = 'disetujui';
        
        $peminjaman = Peminjaman::create($validated);

        LogAktivitas::catat('ajukan_peminjaman', 'Admin menginput peminjaman untuk: ' . $fasilitas->nama_fasilitas);

        return redirect()->route('admin.pengembalian.index')
            ->with('pesan_sukses', 'Peminjaman berhasil diinput dan langsung disetujui (berjalan).');
    }

    /* ---- Admin: Input Jadwal Ruangan ---- */

    public function adminCreateRuangan()
    {
        // Hanya ambil fasilitas yang jenis kategorinya 'ruangan'
        $fasilitas = Fasilitas::with('kategori')
            ->where('status', 'tersedia')
            ->whereHas('kategori', function($q) {
                $q->where('jenis', 'ruangan');
            })
            ->get();
            
        $pengguna = \App\Models\Pengguna::whereNotNull('jenis_pengguna')->get();

        return view('admin.jadwal.buat', compact('fasilitas', 'pengguna'));
    }

    public function adminStoreRuangan(Request $request)
    {
        $validated = $request->validate([
            'pengguna_id' => 'required|exists:pengguna,id',
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tujuan_peminjaman' => 'required|string|max:1000',
        ]);

        $fasilitas = Fasilitas::with('kategori')->findOrFail($validated['fasilitas_id']);

        // Cek konflik jadwal
        if (!$fasilitas->tersediaPadaWaktu($validated['tanggal_mulai'], $validated['tanggal_selesai'])) {
            return back()->withErrors([
                'tanggal_mulai' => 'Jadwal bentrok! Ruangan ini sudah dipesan/ada jadwal pada waktu tersebut.',
            ])->withInput();
        }

        $validated['status_pengajuan'] = 'disetujui';

        $peminjaman = Peminjaman::create($validated);
        
        LogAktivitas::catat('ajukan_peminjaman', 'Admin menginput jadwal ruangan untuk: ' . $fasilitas->nama_fasilitas);

        return redirect()->route('admin.verifikasi.index')
            ->with('pesan_sukses', 'Jadwal ruangan berhasil diinput dan masuk ke kalender.');
    }
}
