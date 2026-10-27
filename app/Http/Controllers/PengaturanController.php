<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $logo = Pengaturan::where('key', 'logo')->first();
        $namaWebsite = Pengaturan::where('key', 'nama_website')->first();
        $warnaNamaWebsite = Pengaturan::where('key', 'warna_nama_website')->first();
        return view('super_admin.pengaturan.index', compact('logo', 'namaWebsite', 'warnaNamaWebsite'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_website' => 'nullable|string|max:100',
            'warna_nama_website' => 'nullable|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        if ($request->has('nama_website')) {
            $pengaturanNama = Pengaturan::firstOrNew(['key' => 'nama_website']);
            $pengaturanNama->value = $request->nama_website;
            $pengaturanNama->save();
        }

        if ($request->has('warna_nama_website')) {
            $pengaturanWarna = Pengaturan::firstOrNew(['key' => 'warna_nama_website']);
            $pengaturanWarna->value = $request->warna_nama_website;
            $pengaturanWarna->save();
        }

        if ($request->hasFile('logo')) {
            $pengaturan = Pengaturan::firstOrNew(['key' => 'logo']);
            
            // Hapus logo lama jika ada
            if ($pengaturan->exists && $pengaturan->value) {
                Storage::disk('public')->delete($pengaturan->value);
            }

            // Simpan logo baru
            $path = $request->file('logo')->store('pengaturan', 'public');
            $pengaturan->value = $path;
            $pengaturan->save();
        }

        return back()->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}
