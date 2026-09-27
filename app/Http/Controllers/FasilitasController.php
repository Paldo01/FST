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

    public function create()
    {
        $kategoriList = KategoriFasilitas::all();
        return view('admin.fasilitas.form', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_fasilitas,id',
            'kode_fasilitas' => 'required|string|unique:fasilitas,kode_fasilitas',
            'nama_fasilitas' => 'required|string|max:255',
            'status' => 'required|in:tersedia,dipinjam,perbaikan',
            'kondisi_awal' => 'required|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        }

        Fasilitas::create($validated);

        LogAktivitas::catat('tambah_fasilitas', 'Menambahkan fasilitas: ' . $validated['nama_fasilitas']);

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
            'kondisi_awal' => 'required|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($fasilita->gambar) {
                Storage::disk('public')->delete($fasilita->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        }

        $fasilita->update($validated);

        LogAktivitas::catat('ubah_fasilitas', 'Mengubah fasilitas: ' . $fasilita->nama_fasilitas);

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

        return redirect()->route('admin.fasilitas.index')
            ->with('pesan_sukses', 'Fasilitas berhasil dihapus!');
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
