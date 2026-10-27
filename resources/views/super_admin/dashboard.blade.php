@extends('layouts.app')

@section('title', 'Dashboard Super Admin - SIMORA FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Dashboard Super Admin
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Monitoring Sistem Peminjaman Fasilitas FST
        </p>
    </div>

    <!-- 4 Kartu Statistik Utama (Sesuai Mockup) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Kartu 1: Total Admin -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6 sm:w-7 sm:h-7 text-[#EF4444]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Admin</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['total_admin'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Admin</span>
            </div>
        </div>

        <!-- Kartu 2: Total Peminjaman -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#DBEAFE] flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-6 h-6 sm:w-7 sm:h-7 text-[#2563EB]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Peminjaman</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['total_peminjaman'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Peminjaman</span>
            </div>
        </div>

        <!-- Kartu 3: Total Ruangan -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#D1FAE5] flex items-center justify-center shrink-0">
                <i data-lucide="door-open" class="w-6 h-6 sm:w-7 sm:h-7 text-[#10B981]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Ruangan</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['total_ruangan'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Ruang</span>
            </div>
        </div>

        <!-- Kartu 4: Total Peralatan -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <i data-lucide="package" class="w-6 h-6 sm:w-7 sm:h-7 text-[#F59E0B]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Peralatan</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['total_peralatan'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Alat</span>
            </div>
        </div>

    </div>

    <!-- Bagian Tengah: Status Peminjaman & Aktivitas Terbaru (Sesuai Mockup) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- Kolom Kiri: Status Peminjaman (lg:col-span-5) -->
        <div class="lg:col-span-5 bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 flex flex-col justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight mb-5">
                    Status Peminjaman
                </h2>

                <div class="space-y-3">
                    <!-- 1. Pending (Kuning) -->
                    <div class="bg-[#F8FAFC] hover:bg-gray-100/70 border border-gray-100/80 rounded-xl sm:rounded-2xl py-3 px-4 sm:px-5 flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#F59E0B] shrink-0"></span>
                            <span class="text-sm sm:text-base font-bold text-gray-900">Pending</span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-gray-500 bg-white px-2.5 py-0.5 rounded-lg border border-gray-100 shadow-2xs">
                            {{ $statusCounts['pending'] }}
                        </span>
                    </div>

                    <!-- 2. Disetujui (Hijau) -->
                    <div class="bg-[#F8FAFC] hover:bg-gray-100/70 border border-gray-100/80 rounded-xl sm:rounded-2xl py-3 px-4 sm:px-5 flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#10B981] shrink-0"></span>
                            <span class="text-sm sm:text-base font-bold text-gray-900">Disetujui</span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-gray-500 bg-white px-2.5 py-0.5 rounded-lg border border-gray-100 shadow-2xs">
                            {{ $statusCounts['disetujui'] }}
                        </span>
                    </div>

                    <!-- 3. Ditolak (Merah) -->
                    <div class="bg-[#F8FAFC] hover:bg-gray-100/70 border border-gray-100/80 rounded-xl sm:rounded-2xl py-3 px-4 sm:px-5 flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#EF4444] shrink-0"></span>
                            <span class="text-sm sm:text-base font-bold text-gray-900">Ditolak</span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-gray-500 bg-white px-2.5 py-0.5 rounded-lg border border-gray-100 shadow-2xs">
                            {{ $statusCounts['ditolak'] }}
                        </span>
                    </div>

                    <!-- 4. Selesai (Biru) -->
                    <div class="bg-[#F8FAFC] hover:bg-gray-100/70 border border-gray-100/80 rounded-xl sm:rounded-2xl py-3 px-4 sm:px-5 flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#2563EB] shrink-0"></span>
                            <span class="text-sm sm:text-base font-bold text-gray-900">Selesai</span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-gray-500 bg-white px-2.5 py-0.5 rounded-lg border border-gray-100 shadow-2xs">
                            {{ $statusCounts['selesai'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Link Cepat ke Riwayat -->
            <div class="pt-5 mt-4 border-t border-gray-100/80">
                <a href="{{ route('admin.riwayat.index') }}" 
                   class="flex items-center justify-between text-xs sm:text-sm font-bold text-[#801414] hover:underline">
                    <span>Lihat Seluruh Riwayat Peminjaman</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Aktivitas Terbaru (lg:col-span-7) -->
        <div class="lg:col-span-7 bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 flex flex-col justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight mb-5">
                    Aktivitas Terbaru
                </h2>

                <div class="space-y-4">
                    @foreach($aktivitasTerbaru as $aktivitas)
                    <div class="flex items-center justify-between gap-3 py-2 border-b border-gray-100/70 last:border-0">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full {{ $aktivitas['bg_color'] }} flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $aktivitas['icon'] }}" class="w-4 h-4 sm:w-5 sm:h-5 {{ $aktivitas['icon_color'] }}"></i>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-800 truncate">
                                {{ $aktivitas['judul'] }}
                            </p>
                        </div>
                        <span class="text-[11px] sm:text-xs text-gray-400 font-normal shrink-0">
                            {{ $aktivitas['waktu'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Link Cepat ke Audit Log -->
            <div class="pt-5 mt-4 border-t border-gray-100/80 flex items-center justify-between">
                <span class="text-xs text-gray-400">Total riwayat tercatat di Audit Log</span>
                <a href="{{ route('super_admin.log.index') }}" 
                   class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#801414] hover:underline">
                    <span>Semua Log Aktivitas</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Bagian Bawah: Ringkasan Sistem (Sesuai Mockup) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7">
        <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight mb-5">
            Ringkasan Sistem
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 gap-6 md:gap-0">
            
            <!-- Segment 1: Admin Aktif -->
            <div class="flex items-center gap-4 py-2 md:py-0 md:px-6 first:pl-0">
                <div class="w-12 h-12 rounded-full bg-[#FEE2E2] flex items-center justify-center shrink-0">
                    <i data-lucide="user" class="w-6 h-6 text-[#EF4444]"></i>
                </div>
                <div>
                    <h4 class="text-base sm:text-lg font-black text-gray-900 leading-tight">
                        {{ $ringkasanSistem['admin_aktif'] }} Admin aktif
                    </h4>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Dari seluruh role
                    </p>
                </div>
            </div>

            <!-- Segment 2: Feedback -->
            <div class="flex items-center gap-4 py-2 md:py-0 md:px-6">
                <div class="w-12 h-12 rounded-2xl bg-[#DBEAFE] flex items-center justify-center shrink-0">
                    <i data-lucide="message-square" class="w-6 h-6 text-[#2563EB]"></i>
                </div>
                <div>
                    <h4 class="text-base sm:text-lg font-black text-gray-900 leading-tight">
                        {{ $ringkasanSistem['total_feedback'] }} Feedback
                    </h4>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Total masukan pengguna
                    </p>
                </div>
            </div>

            <!-- Segment 3: Audit Log Terbaru -->
            <div class="flex items-center gap-4 py-2 md:py-0 md:px-6 last:pr-0">
                <div class="w-12 h-12 rounded-2xl bg-[#F3E8FF] flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="w-6 h-6 text-[#9333EA]"></i>
                </div>
                <div>
                    <h4 class="text-base sm:text-lg font-black text-gray-900 leading-tight">
                        Audit log terbaru
                    </h4>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        {{ $ringkasanSistem['audit_log_terbaru'] }}
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
