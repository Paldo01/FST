<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan data pengaturan website ke semua view Blade
        View::composer('*', function ($view) {
            $view->with([
                'appNamaWebsite'      => Pengaturan::get('nama_website', 'Sistem Peminjaman FST'),
                'appSubNamaWebsite'   => Pengaturan::get('sub_nama_website', 'Sistem'),
                'appWarnaNamaWebsite' => Pengaturan::get('warna_nama_website', '#801414'),
                'appDeskripsi'        => Pengaturan::get('deskripsi_website', 'Sistem Manajemen Peminjaman Ruang dan Alat Fakultas Sains dan Teknologi UIN Imam Bonjol Padang'),
                'appEmail'            => Pengaturan::get('kontak_email', 'fst@uinib.ac.id'),
                'appTelepon'          => Pengaturan::get('kontak_telepon', '0751-123456'),
                'appAlamat'           => Pengaturan::get('alamat', 'Kampus III UIN Imam Bonjol Padang, Sungai Bangek, Kota Padang'),
                'appLogo'             => Pengaturan::logoUrl(),
                'appFavicon'          => Pengaturan::faviconUrl(),
            ]);
        });
    }
}

