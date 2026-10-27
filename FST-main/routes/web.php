<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\UlasanController;
use Illuminate\Support\Facades\Route;

// Halaman Publik
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// API Publik
Route::prefix('api/fasilitas')->name('api.fasilitas.')->group(function () {
    Route::get('/ketersediaan', [BerandaController::class, 'ketersediaan'])->name('ketersediaan');
    Route::get('/cari', [BerandaController::class, 'cariFasilitas'])->name('cari');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('masuk');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login');
    
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('daftar');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register');

    // Lupa Sandi
    Route::get('/lupa-sandi', [\App\Http\Controllers\LupaSandiController::class, 'showLupaSandi'])->name('lupa_sandi');
    Route::post('/lupa-sandi', [\App\Http\Controllers\LupaSandiController::class, 'kirimOtp'])->name('lupa_sandi.kirim');
    Route::get('/verifikasi-otp', [\App\Http\Controllers\LupaSandiController::class, 'showVerifikasiOtp'])->name('lupa_sandi.verifikasi');
    Route::post('/verifikasi-otp', [\App\Http\Controllers\LupaSandiController::class, 'verifikasiOtp'])->name('lupa_sandi.verifikasi.submit');
    Route::get('/reset-sandi', [\App\Http\Controllers\LupaSandiController::class, 'showResetSandi'])->name('lupa_sandi.reset');
    Route::post('/reset-sandi', [\App\Http\Controllers\LupaSandiController::class, 'resetSandi'])->name('lupa_sandi.reset.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('keluar');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role: Pengguna (Mahasiswa/Dosen)
    Route::middleware('peran:pengguna,admin,super_admin')->group(function () {
        Route::resource('peminjaman', PeminjamanController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('/api/cek-ketersediaan', [PeminjamanController::class, 'cekKetersediaan'])->name('api.cek_ketersediaan');
        
        // Katalog Fasilitas
        Route::get('/katalog/ruang', [BerandaController::class, 'katalogRuang'])->name('katalog.ruang');
        Route::get('/katalog/alat', [BerandaController::class, 'katalogAlat'])->name('katalog.alat');
        
        Route::get('/peminjaman/{peminjaman}/ulasan', [UlasanController::class, 'create'])->name('ulasan.buat');
        Route::post('/peminjaman/{peminjaman}/ulasan', [UlasanController::class, 'store'])->name('ulasan.simpan');
    });

    // Role: Admin (Pengelola Fasilitas)
    Route::middleware('peran:admin,super_admin')->prefix('admin')->name('admin.')->group(function () {
        // Fasilitas & Kategori
        Route::resource('fasilitas', FasilitasController::class);
        Route::get('kategori', [FasilitasController::class, 'kategoriIndex'])->name('kategori.index');
        Route::post('kategori', [FasilitasController::class, 'kategoriStore'])->name('kategori.store');
        Route::put('kategori/{kategori}', [FasilitasController::class, 'kategoriUpdate'])->name('kategori.update');
        Route::delete('kategori/{kategori}', [FasilitasController::class, 'kategoriDestroy'])->name('kategori.destroy');

        // Peminjaman (Admin Input)
        Route::get('peminjaman/buat', [PeminjamanController::class, 'adminCreate'])->name('peminjaman.buat');
        Route::post('peminjaman', [PeminjamanController::class, 'adminStore'])->name('peminjaman.store');

        // Verifikasi Peminjaman
        Route::get('verifikasi', [PeminjamanController::class, 'verifikasiIndex'])->name('verifikasi.index');
        Route::post('verifikasi/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])->name('verifikasi.setujui');
        Route::post('verifikasi/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])->name('verifikasi.tolak');

        // Pengembalian
        Route::get('pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::post('pengembalian/{peminjaman}', [PengembalianController::class, 'store'])->name('pengembalian.store');

        // Riwayat Peminjaman (semua pengguna)
        Route::get('riwayat', [PeminjamanController::class, 'adminRiwayat'])->name('riwayat.index');

        // Laporan
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/cetak', [LaporanController::class, 'generate'])->name('laporan.cetak');
    });

    // Role: Super Admin (Pimpinan)
    Route::middleware('peran:super_admin')->prefix('super-admin')->name('super_admin.')->group(function () {
        Route::post('pengguna/{id}/setujui', [PenggunaController::class, 'setujui'])->name('pengguna.setujui');
        Route::resource('pengguna', PenggunaController::class)->except(['create', 'show', 'edit']);
        Route::get('log', [PenggunaController::class, 'logAktivitas'])->name('log.index');
        Route::get('ulasan', [UlasanController::class, 'indexAdmin'])->name('ulasan.index');
    });
});
