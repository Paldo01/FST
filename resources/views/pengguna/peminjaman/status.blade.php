@extends('layouts.app')

@section('title', 'Status Peminjaman - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2">

    <!-- Header Halaman -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Status Peminjaman
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Pantau progres verifikasi dan peminjaman yang sedang aktif secara real-time.
        </p>
    </div>

    <!-- Tab Navigasi Turunan (Status vs Riwayat) -->
    <div class="flex items-center gap-2 p-1.5 bg-gray-100/80 rounded-2xl border border-gray-200/70 w-full sm:w-fit">
        <!-- Tab 1: Status Peminjaman (Aktif) -->
        <a href="{{ route('peminjaman.status') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-[#801414] text-white shadow-xs transition-all">
            <i data-lucide="clock" class="w-4 h-4"></i>
            <span>Status Peminjaman</span>
            <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-black">
                {{ $countMenunggu + $countDisetujui }}
            </span>
        </a>

        <!-- Tab 2: Riwayat Peminjaman (Selesai/Lampau) -->
        <a href="{{ route('peminjaman.riwayat') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all">
            <i data-lucide="history" class="w-4 h-4 text-gray-500"></i>
            <span>Riwayat Peminjaman</span>
            <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 text-[11px] font-black">
                {{ $countRiwayat }}
            </span>
        </a>
    </div>

    <!-- Ringkasan Statistik Status Aktif -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Card 1: Menunggu Verifikasi -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-amber-200/80 p-5 sm:p-6 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-amber-800">Menunggu Verifikasi</p>
                <p class="text-3xl font-black text-amber-900 tracking-tight">{{ $countMenunggu }}</p>
                <p class="text-xs text-amber-700 font-medium">Pengajuan sedang ditinjau oleh Admin FST</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <i data-lucide="hourglass" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 2: Disetujui / Sedang Berjalan -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-emerald-200/80 p-5 sm:p-6 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-emerald-800">Disetujui / Sedang Berjalan</p>
                <p class="text-3xl font-black text-emerald-900 tracking-tight">{{ $countDisetujui }}</p>
                <p class="text-xs text-emerald-700 font-medium">Fasilitas siap atau sedang digunakan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Daftar Peminjaman Aktif -->
    @if($peminjamanAktif->isEmpty())
        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-2xs p-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#801414] flex items-center justify-center mx-auto mb-2">
                <i data-lucide="calendar-check-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-black text-gray-900">Tidak Ada Peminjaman yang Sedang Berjalan</h3>
            <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto font-medium">
                Saat ini Anda tidak memiliki pengajuan yang sedang menunggu verifikasi atau aktif digunakan.
            </p>
            <div class="pt-2 flex items-center justify-center gap-3">
                <a href="{{ route('katalog.ruang') }}" 
                   class="px-5 py-2.5 bg-[#801414] hover:bg-[#681010] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-all flex items-center gap-2">
                    <i data-lucide="building" class="w-4 h-4"></i>
                    <span>Pilih di Katalog Ruang</span>
                </a>
                <a href="{{ route('peminjaman.riwayat') }}" 
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-bold rounded-xl transition-all flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>Lihat Riwayat Selesai</span>
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($peminjamanAktif as $p)
                @php
                    $isMenunggu = $p->status_pengajuan === 'menunggu';
                    $isDisetujui = $p->status_pengajuan === 'disetujui';

                    // Resolve image
                    $f = $p->fasilitas;
                    $imgUrl = null;
                    if ($f && $f->gambar && file_exists(public_path($f->gambar))) {
                        $imgUrl = asset($f->gambar);
                    } elseif ($f && $f->gambar && file_exists(public_path('storage/' . $f->gambar))) {
                        $imgUrl = asset('storage/' . $f->gambar);
                    } else {
                        $namaLower = strtolower($f ? $f->nama_fasilitas : '');
                        if (str_contains($namaLower, 'aula')) {
                            $imgUrl = asset('images/aula-fst.jpg');
                        } elseif (str_contains($namaLower, 'lab')) {
                            $imgUrl = asset('images/lab-komputer.jpg');
                        } elseif (str_contains($namaLower, 'infocus') || str_contains($namaLower, 'proyektor')) {
                            $imgUrl = asset('images/infocus.jpg');
                        } elseif (str_contains($namaLower, 'sound')) {
                            $imgUrl = asset('images/sound-system.jpg');
                        } elseif (str_contains($namaLower, 'mic')) {
                            $imgUrl = asset('images/mikrofon.jpg');
                        } else {
                            $imgUrl = asset('images/ruang-kelas.jpg');
                        }
                    }
                @endphp

                <!-- Card Peminjaman Aktif -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs overflow-hidden transition-all hover:shadow-md">
                    <!-- Header Card -->
                    <div class="px-5 py-4 bg-gray-50/70 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono font-bold text-gray-800 shadow-2xs">
                                {{ $p->kode_peminjaman ?: 'PMJ-FST-' . str_pad($p->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-xs text-gray-500 font-medium">
                                Diajukan pada {{ $p->created_at ? $p->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($isMenunggu)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-full border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Menunggu Verifikasi Admin</span>
                                </span>
                            @elseif($isDisetujui)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Disetujui (Aktif Berjalan)</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Body Card -->
                    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <!-- Foto Fasilitas -->
                        <div class="md:col-span-3">
                            <div class="h-32 sm:h-36 rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 relative">
                                <img src="{{ $imgUrl }}" alt="{{ $f ? $f->nama_fasilitas : 'Fasilitas' }}" class="w-full h-full object-cover">
                                <span class="absolute bottom-2 left-2 px-2.5 py-0.5 rounded-md bg-black/70 backdrop-blur-xs text-white text-[11px] font-bold">
                                    {{ $f && $f->kategori ? $f->kategori->nama_kategori : 'Fasilitas' }}
                                </span>
                            </div>
                        </div>

                        <!-- Info Detail -->
                        <div class="md:col-span-9 space-y-4">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 tracking-tight">
                                    {{ $f ? $f->nama_fasilitas : 'Fasilitas FST' }}
                                </h3>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">
                                    Kode Alat/Ruang: <span class="font-mono font-bold text-gray-700">{{ $f ? $f->kode_fasilitas : '-' }}</span>
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                                <div class="space-y-1">
                                    <span class="text-gray-400 font-medium">Jadwal Penggunaan:</span>
                                    <p class="font-bold text-gray-800 flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                                        <span>{{ \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y (H:i)') }} - {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y (H:i)') }}</span>
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-gray-400 font-medium">Tujuan Peminjaman:</span>
                                    <p class="font-bold text-gray-800 flex items-center gap-1.5 line-clamp-2">
                                        <i data-lucide="target" class="w-4 h-4 text-gray-500 shrink-0"></i>
                                        <span>{{ $p->tujuan_peminjaman }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Live Progress Stepper -->
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Progres Verifikasi & Layanan</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <!-- Step 1: Pengajuan -->
                                    <div class="flex items-center gap-2 p-2 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800">
                                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                        <span class="text-xs font-bold truncate">1. Pengajuan</span>
                                    </div>

                                    <!-- Step 2: Verifikasi -->
                                    <div class="flex items-center gap-2 p-2 rounded-xl border {{ $isDisetujui ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                                        <i data-lucide="{{ $isDisetujui ? 'check-circle' : 'clock' }}" class="w-4 h-4 shrink-0 {{ $isDisetujui ? 'text-emerald-600' : 'text-amber-600 animate-spin' }}"></i>
                                        <span class="text-xs font-bold truncate">2. {{ $isDisetujui ? 'Disetujui' : 'Menunggu' }}</span>
                                    </div>

                                    <!-- Step 3: Penggunaan -->
                                    <div class="flex items-center gap-2 p-2 rounded-xl border {{ $isDisetujui ? 'bg-blue-50 border-blue-100 text-blue-800' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                                        <i data-lucide="{{ $isDisetujui ? 'door-open' : 'clock' }}" class="w-4 h-4 shrink-0"></i>
                                        <span class="text-xs font-bold truncate">3. {{ $isDisetujui ? 'Aktif' : 'Menunggu' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="px-5 py-3.5 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="text-xs text-gray-500 font-medium">
                            @if($isMenunggu)
                                <span>* Pengajuan dapat dibatalkan selama belum diverifikasi oleh admin.</span>
                            @else
                                <span class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                    Silakan gunakan fasilitas sesuai jadwal yang telah disetujui.
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            @if($isMenunggu)
                                <form method="POST" action="{{ route('peminjaman.batalkan', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                                    @csrf
                                    <button type="submit" 
                                            class="px-4 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                        Batalkan Pengajuan
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('peminjaman.show', $p->id) }}" 
                               class="px-4 py-2 bg-[#801414] hover:bg-[#681010] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <span>Detail Peminjaman</span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
