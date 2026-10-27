@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2">

    <!-- Header Halaman -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Riwayat Peminjaman
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Arsip seluruh catatan peminjaman ruangan dan peralatan yang telah selesai atau dibatalkan.
        </p>
    </div>

    <!-- Tab Navigasi Turunan (Status vs Riwayat) -->
    <div class="flex items-center gap-2 p-1.5 bg-gray-100/80 rounded-2xl border border-gray-200/70 w-full sm:w-fit">
        <!-- Tab 1: Status Peminjaman (Aktif) -->
        <a href="{{ route('peminjaman.status') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all">
            <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
            <span>Status Peminjaman</span>
            <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 text-[11px] font-black">
                {{ $countAktif }}
            </span>
        </a>

        <!-- Tab 2: Riwayat Peminjaman (Selesai/Lampau) -->
        <a href="{{ route('peminjaman.riwayat') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-[#801414] text-white shadow-xs transition-all">
            <i data-lucide="history" class="w-4 h-4"></i>
            <span>Riwayat Peminjaman</span>
            <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-black">
                {{ $riwayatList->total() }}
            </span>
        </a>
    </div>

    <!-- Ringkasan Statistik Riwayat -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Card 1: Peminjaman Selesai -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-emerald-200/80 p-5 sm:p-6 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-emerald-800">Peminjaman Selesai</p>
                <p class="text-3xl font-black text-emerald-900 tracking-tight">{{ $countSelesai }}</p>
                <p class="text-xs text-emerald-700 font-medium">Fasilitas telah digunakan dan dikembalikan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 2: Ditolak / Dibatalkan -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-red-200/80 p-5 sm:p-6 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-red-800">Ditolak / Dibatalkan</p>
                <p class="text-3xl font-black text-red-900 tracking-tight">{{ $countDitolak }}</p>
                <p class="text-xs text-red-700 font-medium">Permintaan tidak disetujui atau dibatalkan pemohon</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center shrink-0">
                <i data-lucide="x-circle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar Riwayat -->
    <form method="GET" action="{{ route('peminjaman.riwayat') }}" 
          class="bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center gap-3">
        
        <!-- Input Pencarian -->
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input type="text" 
                   name="cari" 
                   value="{{ request('cari') }}" 
                   placeholder="Cari kode, nama fasilitas, atau keperluan..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
        </div>

        <!-- Filter Status -->
        <div class="relative md:w-48 shrink-0">
            <i data-lucide="filter" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"></i>
            <select name="status" 
                    onchange="this.form.submit()"
                    class="w-full pl-10 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] appearance-none cursor-pointer transition-all">
                <option value="">Semua Status</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>

        <!-- Filter Bulan -->
        <div class="relative md:w-48 shrink-0">
            <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"></i>
            <input type="month" 
                   name="bulan" 
                   value="{{ request('bulan') }}" 
                   onchange="this.form.submit()"
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
        </div>

        <!-- Tombol Cari & Reset -->
        <div class="flex items-center gap-2 shrink-0">
            <button type="submit" 
                    class="w-full md:w-auto px-6 py-2.5 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Cari</span>
            </button>

            @if(request('cari') || request('status') || request('bulan'))
                <a href="{{ route('peminjaman.riwayat') }}" 
                   class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl sm:rounded-2xl transition-all flex items-center justify-center" 
                   title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Daftar Riwayat -->
    @if($riwayatList->isEmpty())
        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-2xs p-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-2">
                <i data-lucide="history" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-black text-gray-900">Belum Ada Riwayat Peminjaman</h3>
            <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto font-medium">
                Peminjaman yang telah selesai, dikembalikan, atau ditolak akan dicatat secara otomatis di halaman ini sebagai arsip Anda.
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($riwayatList as $p)
                @php
                    $isSelesai = $p->status_pengajuan === 'selesai';
                    $isDitolak = $p->status_pengajuan === 'ditolak';
                    $isDibatalkan = $p->status_pengajuan === 'dibatalkan';

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

                <!-- Card Riwayat -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs overflow-hidden transition-all hover:shadow-md">
                    <!-- Header Card -->
                    <div class="px-5 py-3.5 bg-gray-50/70 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono font-bold text-gray-800 shadow-2xs">
                                {{ $p->kode_peminjaman ?: 'PMJ-FST-' . str_pad($p->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-xs text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($isSelesai)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Peminjaman Selesai</span>
                                </span>
                            @elseif($isDitolak)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full border border-red-200">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    <span>Ditolak Admin</span>
                                </span>
                            @elseif($isDibatalkan)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full border border-gray-200">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    <span>Dibatalkan Pemohon</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Body Card -->
                    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                        <!-- Foto Fasilitas -->
                        <div class="md:col-span-3">
                            <div class="h-28 sm:h-32 rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 relative">
                                <img src="{{ $imgUrl }}" alt="{{ $f ? $f->nama_fasilitas : 'Fasilitas' }}" class="w-full h-full object-cover">
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-xs text-white text-[10px] font-bold">
                                    {{ $f && $f->kategori ? $f->kategori->nama_kategori : 'Fasilitas' }}
                                </span>
                            </div>
                        </div>

                        <!-- Detail Info -->
                        <div class="md:col-span-9 space-y-3">
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">
                                    {{ $f ? $f->nama_fasilitas : 'Fasilitas FST' }}
                                </h3>
                                <p class="text-xs text-gray-500 font-medium">
                                    Kode Alat/Ruang: <span class="font-mono font-bold text-gray-700">{{ $f ? $f->kode_fasilitas : '-' }}</span>
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm">
                                <div>
                                    <span class="text-gray-400 font-medium block">Waktu Penggunaan:</span>
                                    <span class="font-bold text-gray-800">
                                        {{ \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y (H:i)') }} - {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('H:i') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium block">Tujuan:</span>
                                    <span class="font-semibold text-gray-800 line-clamp-1">{{ $p->tujuan_peminjaman }}</span>
                                </div>
                            </div>

                            @if($isDitolak && $p->alasan_penolakan)
                                <div class="p-2.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800">
                                    <span class="font-bold">Alasan Penolakan:</span> {{ $p->alasan_penolakan }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Card: Ulasan & Tombol Detail -->
                    <div class="px-5 py-3.5 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            @if($isSelesai)
                                @if($p->ulasan)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 text-xs font-bold rounded-lg border border-amber-200">
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-500"></i>
                                        <span>Sudah Diulas (Rating: {{ $p->ulasan->rating }}/5)</span>
                                    </span>
                                @else
                                    <a href="{{ route('ulasan.buat', $p->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-2xs transition">
                                        <i data-lucide="star" class="w-3.5 h-3.5"></i>
                                        <span>Beri Feedback & Ulasan</span>
                                    </a>
                                @endif
                            @endif
                        </div>

                        <div class="self-end sm:self-auto">
                            <a href="{{ route('peminjaman.show', $p->id) }}" 
                               class="px-4 py-2 bg-[#801414] hover:bg-[#681010] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <span>Detail Peminjaman</span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-2">
                {{ $riwayatList->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
