@extends('layouts.app')

@section('title', 'Beranda - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 sm:space-y-7 pb-12 pt-2">

    <!-- Header Banner Selamat Datang (Sesuai Mockup) -->
    <div class="text-center py-2 sm:py-4">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center justify-center gap-2">
            <span>Selamat Datang di Sistem Peminjaman FST</span>
            <span class="text-2xl sm:text-3xl select-none inline-block">👋</span>
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1.5">
            Kelola peminjaman ruang dan alat dengan mudah.
        </p>
    </div>

    <!-- 2 Kartu Katalog Fasilitas (Sesuai Mockup) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
        
        <!-- Kartu 1: Katalog Ruang -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs p-6 sm:p-7 flex items-center gap-5 sm:gap-6 hover:shadow-xs transition-all">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                <i data-lucide="building" class="w-8 h-8 sm:w-9 sm:h-9 text-[#801414]"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Katalog Ruang</h3>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Lihat ruang yang tersedia untuk dipinjam</p>
                <div class="mt-4">
                    <a href="{{ route('katalog.ruang') }}" 
                       class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl shadow-xs hover:shadow-md transition-all cursor-pointer">
                        <span>Lihat Ruang</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Katalog Alat -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs p-6 sm:p-7 flex items-center gap-5 sm:gap-6 hover:shadow-xs transition-all">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                <i data-lucide="monitor" class="w-8 h-8 sm:w-9 sm:h-9 text-[#801414]"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Katalog Alat</h3>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Lihat alat yang tersedia untuk dipinjam</p>
                <div class="mt-4">
                    <a href="{{ route('katalog.alat') }}" 
                       class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl shadow-xs hover:shadow-md transition-all cursor-pointer">
                        <span>Lihat Alat</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Seksi Peminjaman Saya (Sesuai Mockup) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs p-6 sm:p-7 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                Peminjaman Saya
            </h2>
            <a href="{{ route('peminjaman.index') }}" 
               class="text-xs sm:text-sm font-bold text-[#801414] hover:text-[#681010] hover:underline flex items-center gap-1 transition-colors">
                <span>Lihat Semua</span>
                <span>→</span>
            </a>
        </div>

        @if($peminjamanAktif->isEmpty())
            <!-- Tampilan Kosong Sesuai Mockup -->
            <div class="rounded-2xl bg-[#F8FAFC] border border-slate-100 py-12 px-6 text-center">
                <div class="w-12 h-12 mx-auto mb-3.5 rounded-xl border border-red-200 bg-red-50 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-[#DC2626]"></i>
                </div>
                <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">
                    Tidak ada peminjaman aktif
                </h3>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
                    Mulai ajukan peminjaman ruang atau alat sekarang.
                </p>
            </div>
        @else
            <!-- Daftar Peminjaman Aktif jika ada -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($peminjamanAktif as $p)
                    <div class="p-4 rounded-2xl bg-gray-50/90 border border-gray-100 flex items-center justify-between hover:bg-gray-100/70 transition-colors">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $p->fasilitas->kategori && $p->fasilitas->kategori->jenis === 'ruangan' ? 'door-open' : 'monitor' }}" class="w-5 h-5 text-[#801414]"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">{{ $p->fasilitas->nama_fasilitas }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5 font-medium">
                                    {{ $p->tanggal_mulai->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-badge :status="$p->status_pengajuan" />
                            <a href="{{ route('peminjaman.show', $p->id) }}" class="p-1.5 text-gray-500 hover:text-[#801414] rounded-lg transition-colors" title="Lihat Detail">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2 Kartu Navigasi Bawah (Sesuai Mockup) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
        
        <!-- Riwayat Peminjaman -->
        <a href="{{ route('peminjaman.index') }}" 
           class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs p-5 sm:p-6 flex items-center justify-between hover:shadow-xs hover:border-gray-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#FEE2E2] flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-6 h-6 text-[#DC2626]"></i>
                </div>
                <div>
                    <h4 class="text-sm sm:text-base font-black text-gray-900 group-hover:text-[#801414] transition-colors">
                        Riwayat Peminjaman
                    </h4>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Lihat riwayat peminjaman ruang dan alat
                    </p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-[#801414] group-hover:translate-x-1 transition-all shrink-0"></i>
        </a>

        <!-- Presensi & Feedback -->
        <a href="{{ route('ulasan.pengguna') }}" 
           class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs p-5 sm:p-6 flex items-center justify-between hover:shadow-xs hover:border-gray-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#FEE2E2] flex items-center justify-center shrink-0">
                    <i data-lucide="message-square" class="w-6 h-6 text-[#DC2626]"></i>
                </div>
                <div>
                    <h4 class="text-sm sm:text-base font-black text-gray-900 group-hover:text-[#801414] transition-colors">
                        Presensi & Feedback
                    </h4>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Berikan presensi dan feedback setelah peminjaman
                    </p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-[#801414] group-hover:translate-x-1 transition-all shrink-0"></i>
        </a>

    </div>

</div>
@endsection
