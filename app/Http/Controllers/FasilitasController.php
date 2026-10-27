<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\KategoriFasilitas;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FasilitasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->jenis === 'ruangan' || $request->routeIs('admin.ruang.*')) {
            return $this->ruangIndex($request);
        }

        if ($request->jenis === 'peralatan' || $request->routeIs('admin.alat.*')) {
            return $this->alatIndex($request);
        }

        $fasilitas = Fasilitas::with('kategori')
            ->when($request->cari, function ($q) use ($request) {
                $q->where('nama_fasilitas', 'like', "%{$request->cari}%")
                    ->orWhere('kode_fasilitas', 'like', "%{$request->cari}%");
            })
            ->when($request->jenis, function ($q) use ($request) {
                $q->whereHas('kategori', fn($sub) => $sub->where('jenis', $request->jenis));
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        $kategoriList = KategoriFasilitas::all();

        return view('admin.fasilitas.index', compact('fasilitas', 'kategoriList'));
    }

    public function ruangIndex(Request $request)
    {
        $query = Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'ruangan'))
            ->with('kategori');

        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_fasilitas', 'like', "%{$request->cari}%")
                  ->orWhere('kode_fasilitas', 'like', "%{$request->cari}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $ruangan = $query->orderBy('id', 'asc')->paginate(20);
        $kategoriList = KategoriFasilitas::where('jenis', 'ruangan')->orderBy('nama_kategori')->get();

        return view('admin.ruang.index', compact('ruangan', 'kategoriList'));
    }

    public function alatIndex(Request $request)
    {
        $query = Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'peralatan'))
            ->with('kategori');

        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_fasilitas', 'like', "%{$request->cari}%")
                  ->orWhere('kode_fasilitas', 'like', "%{$request->cari}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $alat = $query->orderBy('id', 'asc')->paginate(20);
        $kategoriList = KategoriFasilitas::where('jenis', 'peralatan')->orderBy('nama_kategori')->get();

        return view('admin.alat.index', compact('alat', 'kategoriList'));
    }

    public function create()
    {
        $kategoriList = KategoriFasilitas::all();
        return view('admin.fasilitas.form', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_fasilitas,id',
            'kode_fasilitas' => 'nullable|string|unique:fasilitas,kode_fasilitas',
            'nama_fasilitas' => 'required|string|max:255',
            'status' => 'required|in:tersedia,dipinjam,perbaikan',
            'kondisi_awal' => 'nullable|string',
            'kapasitas' => 'nullable|string|max:100',
            'jumlah' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        if (empty($validated['kode_fasilitas'])) {
            $kategori = KategoriFasilitas::find($validated['kategori_id']);
            $prefix = ($kategori && $kategori->jenis === 'ruangan') ? 'RNG-' : 'ALT-';
            $validated['kode_fasilitas'] = $prefix . strtoupper(substr(uniqid(), -5));
        }

        if (empty($validated['kondisi_awal'])) {
            $validated['kondisi_awal'] = 'baik';
        }

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        }

        $fasilitas = Fasilitas::create($validated);

        LogAktivitas::catat('tambah_fasilitas', 'Menambahkan fasilitas: ' . $validated['nama_fasilitas']);

        if ($request->filled('from_alat') || ($fasilitas->kategori && $fasilitas->kategori->jenis === 'peralatan')) {
            return redirect()->route('admin.alat.index')
                ->with('pesan_sukses', 'Alat berhasil ditambahkan!');
        }

        if ($request->filled('from_ruang') || ($fasilitas->kategori && $fasilitas->kategori->jenis === 'ruangan')) {
            return redirect()->route('admin.ruang.index')
                ->with('pesan_sukses', 'Ruangan berhasil ditambahkan!');
        }

        return redirect()->route('admin.fasilitas.index')
            ->with('pesan_sukses', 'Fasilitas berhasil ditambahkan!');
    }

    public function edit(Fasilitas $fasilita)
    {
        $kategoriList = KategoriFasilitas::all();
        $fasilitas = $fasilita;
        return view('admin.fasilitas.form', compact('fasilitas', 'kategoriList'));
    }

    public function update(Request $request, Fasilitas $fasilita)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_fasilitas,id',
            'kode_fasilitas' => 'required|string|unique:fasilitas,kode_fasilitas,' . $fasilita->id,
            'nama_fasilitas' => 'required|string|max:255',
            'status' => 'required|in:tersedia,dipinjam,perbaikan',
            'kondisi_awal' => 'nullable|string',
            'kapasitas' => 'nullable|string|max:100',
            'jumlah' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        if (empty($validated['kondisi_awal'])) {
            $validated['kondisi_awal'] = $fasilita->kondisi_awal ?? 'baik';
        }

        if ($request->hasFile('gambar')) {
            if ($fasilita->gambar) {
                Storage::disk('public')->delete($fasilita->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        }

        $fasilita->update($validated);

        LogAktivitas::catat('ubah_fasilitas', 'Mengubah fasilitas: ' . $fasilita->nama_fasilitas);

        if ($request->filled('from_alat') || ($fasilita->kategori && $fasilita->kategori->jenis === 'peralatan')) {
            return redirect()->route('admin.alat.index')
                ->with('pesan_sukses', 'Data alat berhasil diperbarui!');
        }

        if ($request->filled('from_ruang') || ($fasilita->kategori && $fasilita->kategori->jenis === 'ruangan')) {
            return redirect()->route('admin.ruang.index')
                ->with('pesan_sukses', 'Data ruangan berhasil diperbarui!');
        }

        return redirect()->route('admin.fasilitas.index')
            ->with('pesan_sukses', 'Fasilitas berhasil diperbarui!');
    }

    public function destroy(Fasilitas $fasilita)
    {
        $nama = $fasilita->nama_fasilitas;

        if ($fasilita->gambar) {
            Storage::disk('public')->delete($fasilita->gambar);
        }

        $fasilita->delete();

        LogAktivitas::catat('hapus_fasilitas', 'Menghapus fasilitas: ' . $nama);

        return back()->with('pesan_sukses', 'Fasilitas ' . $nama . ' berhasil dihapus!');
    }

    /* ---- Kategori Fasilitas ---- */

    public function kategoriIndex()
    {
        $kategori = KategoriFasilitas::withCount('fasilitas')->latest()->get();
        return view('admin.kategori.index', compact('kategori'));
    }

    public function kategoriStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'jenis' => 'required|in:ruangan,peralatan',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriFasilitas::create($validated);

        LogAktivitas::catat('tambah_kategori', 'Menambahkan kategori: ' . $validated['nama_kategori']);

        return back()->with('pesan_sukses', 'Kategori berhasil ditambahkan!');
    }

    public function kategoriUpdate(Request $request, KategoriFasilitas $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'jenis' => 'required|in:ruangan,peralatan',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($validated);

        LogAktivitas::catat('ubah_kategori', 'Mengubah kategori: ' . $kategori->nama_kategori);

        return back()->with('pesan_sukses', 'Kategori berhasil diperbarui!');
    }

    public function kategoriDestroy(KategoriFasilitas $kategori)
    {
        $nama = $kategori->nama_kategori;
        $kategori->delete();

        LogAktivitas::catat('hapus_kategori', 'Menghapus kategori: ' . $nama);

        return back()->with('pesan_sukses', 'Kategori berhasil dihapus!');
    }
}
