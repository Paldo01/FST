<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\KategoriFasilitas;
use App\Models\Fasilitas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Super Admin (Pimpinan / Dekan)
        Pengguna::create([
            'nama_lengkap' => 'Super Admin FST',
            'nomor_identitas' => '198001012005011001',
            'email' => 'superadmin@fst-uinib.ac.id',
            'kata_sandi' => Hash::make('password'),
            'nomor_telepon' => '08111111111',
            'peran' => 'super_admin'
        ]);

        // 2. Akun Admin (Pengelola / Laboran)
        Pengguna::create([
            'nama_lengkap' => 'Admin Pengelola',
            'nomor_identitas' => '199001012015011002',
            'email' => 'admin@fst-uinib.ac.id',
            'kata_sandi' => Hash::make('password'),
            'nomor_telepon' => '08222222222',
            'peran' => 'admin'
        ]);

        // 3. Akun Pengguna Biasa (Mahasiswa)
        Pengguna::create([
            'nama_lengkap' => 'Budi Mahasiswa',
            'nomor_identitas' => '2014020001',
            'email' => 'budi@fst-uinib.ac.id',
            'kata_sandi' => Hash::make('password'),
            'nomor_telepon' => '08333333333',
            'peran' => 'pengguna'
        ]);

        // 4. Kategori Fasilitas
        $kategoriRuanganId = KategoriFasilitas::create([
            'nama_kategori' => 'Ruang Kuliah',
            'jenis' => 'ruangan',
            'deskripsi' => 'Ruangan untuk aktivitas perkuliahan.'
        ])->id;

        $kategoriLabId = KategoriFasilitas::create([
            'nama_kategori' => 'Laboratorium',
            'jenis' => 'ruangan',
            'deskripsi' => 'Laboratorium terpadu untuk praktikum.'
        ])->id;

        $kategoriAlatId = KategoriFasilitas::create([
            'nama_kategori' => 'Alat Elektronik',
            'jenis' => 'peralatan',
            'deskripsi' => 'Proyektor, Laptop, dsb.'
        ])->id;

        // 5. Fasilitas
        Fasilitas::create([
            'kode_fasilitas' => 'RK-101',
            'nama_fasilitas' => 'Ruang Kuliah 101',
            'kategori_id' => $kategoriRuanganId,
            'deskripsi' => 'Kapasitas 40 Orang, Full AC, Whiteboard',
            'status' => 'tersedia',
            'kondisi_awal' => 'baik'
        ]);

        Fasilitas::create([
            'kode_fasilitas' => 'LAB-KOM',
            'nama_fasilitas' => 'Lab Komputer Dasar',
            'kategori_id' => $kategoriLabId,
            'deskripsi' => '40 Unit PC Inti, AC Sentral',
            'status' => 'tersedia',
            'kondisi_awal' => 'baik'
        ]);

        Fasilitas::create([
            'kode_fasilitas' => 'LCD-01',
            'nama_fasilitas' => 'Proyektor Epson LCD-01',
            'kategori_id' => $kategoriAlatId,
            'deskripsi' => 'Epson EB-X400',
            'status' => 'tersedia',
            'kondisi_awal' => 'baik'
        ]);
        
        Fasilitas::create([
            'kode_fasilitas' => 'LCD-02',
            'nama_fasilitas' => 'Proyektor Epson LCD-02',
            'kategori_id' => $kategoriAlatId,
            'deskripsi' => 'Epson EB-X400, Kabel HDMI',
            'status' => 'perbaikan',
            'kondisi_awal' => 'rusak_ringan'
        ]);
    }
}
