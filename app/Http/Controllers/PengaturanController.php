<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = [
            'nama_website'       => Pengaturan::get('nama_website', 'Sistem Peminjaman FST'),
            'sub_nama_website'   => Pengaturan::get('sub_nama_website', 'Sistem'),
            'warna_nama_website' => Pengaturan::get('warna_nama_website', '#801414'),
            'deskripsi_website'  => Pengaturan::get('deskripsi_website', 'Sistem Manajemen Peminjaman Ruang dan Alat Fakultas Sains dan Teknologi UIN Imam Bonjol Padang'),
            'kontak_email'       => Pengaturan::get('kontak_email', 'fst@uinib.ac.id'),
            'kontak_telepon'     => Pengaturan::get('kontak_telepon', '0751-123456'),
            'alamat'             => Pengaturan::get('alamat', 'Kampus III UIN Imam Bonjol Padang, Sungai Bangek, Kota Padang'),
            'logo'               => Pengaturan::where('key', 'logo')->first(),
            'favicon'            => Pengaturan::where('key', 'favicon')->first(),
            'logo_url'           => Pengaturan::logoUrl(),
            'favicon_url'        => Pengaturan::faviconUrl(),
        ];

        return view('super_admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_website'       => 'required|string|max:100',
            'sub_nama_website'   => 'nullable|string|max:50',
            'warna_nama_website' => 'nullable|string|max:7',
            'deskripsi_website'  => 'nullable|string|max:500',
            'kontak_email'       => 'nullable|email|max:100',
            'kontak_telepon'     => 'nullable|string|max:30',
            'alamat'             => 'nullable|string|max:255',
            'logo'               => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'favicon'            => 'nullable|image|mimes:jpeg,png,jpg,svg,ico,webp|max:1024',
        ], [
            'nama_website.required' => 'Nama website wajib diisi.',
            'logo.max'              => 'Ukuran file logo maksimal 2MB.',
            'favicon.max'           => 'Ukuran file favicon maksimal 1MB.',
        ]);

        $textFields = [
            'nama_website',
            'sub_nama_website',
            'warna_nama_website',
            'deskripsi_website',
            'kontak_email',
            'kontak_telepon',
            'alamat',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                Pengaturan::set($field, $request->input($field));
            }
        }

        // Simpan upload Logo
        if ($request->hasFile('logo')) {
            $logoSetting = Pengaturan::where('key', 'logo')->first();
            if ($logoSetting && $logoSetting->value && Storage::disk('public')->exists($logoSetting->value)) {
                Storage::disk('public')->delete($logoSetting->value);
            }

            $path = $request->file('logo')->store('pengaturan', 'public');
            Pengaturan::set('logo', $path);
        }

        // Simpan upload Favicon
        if ($request->hasFile('favicon')) {
            $favSetting = Pengaturan::where('key', 'favicon')->first();
            if ($favSetting && $favSetting->value && Storage::disk('public')->exists($favSetting->value)) {
                Storage::disk('public')->delete($favSetting->value);
            }

            $favPath = $request->file('favicon')->store('pengaturan', 'public');
            Pengaturan::set('favicon', $favPath);
        }

        // Catat Audit Log
        if (auth()->check()) {
            try {
                LogAktivitas::create([
                    'pengguna_id'     => auth()->id(),
                    'tindakan'        => 'ubah_pengaturan',
                    'detail_kegiatan' => 'Memperbarui identitas dan pengaturan website',
                    'alamat_ip'       => $request->ip(),
                    'created_at'      => now(),
                ]);
            } catch (\Exception $e) {
                // Abaikan jika log error
            }
        }

        return redirect()->route('super_admin.pengaturan.index')->with('success', 'Pengaturan website berhasil disimpan dan disinkronkan ke seluruh sistem!');
    }
}

