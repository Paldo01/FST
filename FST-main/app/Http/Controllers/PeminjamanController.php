<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        // Semua peminjaman pada tanggal yang dipilih
        $peminjaman = Peminjaman::where('pengguna_id', auth()->id())
            ->with(['fasilitas.kategori', 'pengembalian', 'ulasan'])
            ->whereDate('tanggal_mulai', $tanggal)
            ->latest('tanggal_mulai')
            ->get();

        // Statistik ringkas
        $totalBulanIni   = $peminjaman->count();
        $sedangBerjalan  = $peminjaman->where('status_pengajuan', 'disetujui')->count();
        $selesai         = $peminjaman->where('status_pengajuan', 'selesai')->count();
        $menunggu        = $peminjaman->where('status_pengajuan', 'menunggu')->count();

        // Group by tanggal (d M Y) agar tampil seperti timeline
        $grouped = $peminjaman->groupBy(fn($p) => $p->tanggal_mulai->format('d F Y'));

        return view('pengguna.peminjaman.index', compact(
            'grouped', 'tanggal', 'totalBulanIni', 'sedangBerjalan', 'selesai', 'menunggu'
        ));
    }

    public function create()
    {
        $fasilitas = Fasilitas::with('kategori')
            ->where('status', 'tersedia')
            ->whereHas('kategori', function($q) {
                $q->where('jenis', 'ruangan');
            })
            ->get();

        return view('pengguna.peminjaman.buat', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tujuan_peminjaman' => 'required|string|max:1000',
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

    public function verifikasiIndex()
    {
        $pengajuan = Peminjaman::with(['pengguna', 'fasilitas.kategori'])
            ->latest()
            ->paginate(15);

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
        $query = Peminjaman::with(['pengguna', 'fasilitas.kategori', 'pengembalian']);

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $tanggal = $request->input('tanggal');
            $query->whereDate('tanggal_mulai', $tanggal);
        } else {
            // Default tanggal hari ini jika tidak mencari text
            $tanggal = null;
        }

        // Filter pencarian nama / NIM / NIP
        $cari = $request->input('cari');
        if ($cari) {
            $query->whereHas('pengguna', function($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('nomor_identitas', 'like', "%{$cari}%");
            });
        } 

        // Limit data agar tidak terlalu berat jika tidak ada filter sama sekali
        if (!$cari && !$request->filled('tanggal')) {
             $tanggal = now()->format('Y-m-d');
             $query->whereDate('tanggal_mulai', $tanggal);
        }

        // Filter opsional berdasarkan jenis (ruangan/peralatan)
        if ($request->filled('jenis')) {
            $query->whereHas('fasilitas.kategori', fn($q) => $q->where('jenis', $request->jenis));
        }

        $semua = $query->latest('tanggal_mulai')->get();

        // Statistik
        $stats = [
            'total'    => $semua->count(),
            'berjalan' => $semua->where('status_pengajuan', 'disetujui')->count(),
            'selesai'  => $semua->where('status_pengajuan', 'selesai')->count(),
            'menunggu' => $semua->where('status_pengajuan', 'menunggu')->count(),
            'ditolak'  => $semua->where('status_pengajuan', 'ditolak')->count(),
        ];

        // Group by tanggal
        $grouped = $semua->groupBy(fn($p) => $p->tanggal_mulai->format('d F Y'));

        return view('admin.riwayat.index', compact('grouped', 'cari', 'tanggal', 'stats'));
    }

    /**
     * API: Cek ketersediaan fasilitas pada rentang waktu tertentu.
     */
    public function cekKetersediaan(Request $request)
    {
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
                ? 'Fasilitas tersedia pada waktu yang dipilih.'
                : 'Fasilitas sudah dipesan pada waktu tersebut.',
        ]);
    }

    /* ---- Admin: Input Peminjaman Langsung ---- */

    public function adminCreate()
    {
        $fasilitas = Fasilitas::with('kategori')
            ->where('status', 'tersedia')
            ->whereHas('kategori', function($q) {
                $q->where('jenis', 'peralatan');
            })
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

        if ($fasilitas->kategori->jenis !== 'peralatan') {
            return back()->withErrors([
                'fasilitas_id' => 'Form ini khusus untuk peminjaman alat/barang.',
            ])->withInput();
        }

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
}
