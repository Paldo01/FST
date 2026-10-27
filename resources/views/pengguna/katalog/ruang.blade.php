@extends('layouts.app')

@section('title', 'Katalog Ruang - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="{ showDetailModal: false, selectedRoom: null }">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Katalog Ruang
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Lihat dan pilih ruang yang tersedia untuk dipinjam.
        </p>
    </div>

    <!-- Filter Bar (Sesuai Mockup) -->
    <form id="filterForm" method="GET" action="{{ route('katalog.ruang') }}" 
          class="bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center gap-3">
        
        @if(request('urutkan'))
            <input type="hidden" name="urutkan" value="{{ request('urutkan') }}">
        @endif

        <!-- Input 1: Cari nama ruang -->
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input type="text" 
                   name="cari" 
                   value="{{ request('cari') }}" 
                   placeholder="Cari nama ruang" 
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
        </div>

        <!-- Input 2: Dropdown Semua Jenis -->
        <div class="relative md:w-52 shrink-0">
            <i data-lucide="building" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"></i>
            <select name="kategori" 
                    id="kategoriSelect"
                    onchange="document.getElementById('filterForm').submit()"
                    class="w-full pl-10 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] appearance-none cursor-pointer transition-all">
                <option value="">Semua Jenis</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                        {{ $kat }}
                    </option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>

        <!-- Input 3: Pilih Tanggal -->
        <div class="relative md:w-52 shrink-0">
            <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"></i>
            <input type="date" 
                   name="tanggal" 
                   value="{{ request('tanggal') }}" 
                   onchange="document.getElementById('filterForm').submit()"
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
        </div>

        <!-- Tombol Cari -->
        <div class="flex items-center gap-2 shrink-0">
            <button type="submit" 
                    class="w-full md:w-auto px-6 py-2.5 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Cari</span>
            </button>

            @if(request('cari') || request('kategori') || request('tanggal') || (request('urutkan') && request('urutkan') !== 'terbaru'))
                <a href="{{ route('katalog.ruang') }}" 
                   class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl sm:rounded-2xl transition-all flex items-center justify-center" 
                   title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Sub-Header: Daftar Ruang & Urutkan (Sesuai Mockup) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <div class="flex items-center flex-wrap gap-2.5">
            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                Daftar Ruang
            </h2>
            <span class="px-2.5 py-0.5 rounded-full bg-[#FEE2E2] text-[#801414] text-xs font-black">
                {{ $fasilitas->count() }} Ruang
            </span>

            @if(request('kategori'))
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs font-bold border border-gray-200">
                    <span>Jenis: {{ request('kategori') }}</span>
                    <a href="{{ route('katalog.ruang', array_merge(request()->except('kategori'))) }}" class="text-gray-400 hover:text-gray-700">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </a>
                </span>
            @endif
        </div>

        <!-- Dropdown Urutkan -->
        <form method="GET" action="{{ route('katalog.ruang') }}" class="flex items-center gap-2" id="sortForm">
            @if(request('cari')) <input type="hidden" name="cari" value="{{ request('cari') }}"> @endif
            @if(request('kategori')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif
            @if(request('tanggal')) <input type="hidden" name="tanggal" value="{{ request('tanggal') }}"> @endif
            
            <div class="relative">
                <i data-lucide="sliders-horizontal" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600 pointer-events-none"></i>
                <select name="urutkan" 
                        onchange="document.getElementById('sortForm').submit()" 
                        class="pl-9 pr-8 py-2 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] appearance-none cursor-pointer">
                    <option value="terbaru" {{ request('urutkan') === 'terbaru' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                    <option value="terlama" {{ request('urutkan') === 'terlama' ? 'selected' : '' }}>Urutkan: Terlama</option>
                    <option value="nama_asc" {{ request('urutkan') === 'nama_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                    <option value="nama_desc" {{ request('urutkan') === 'nama_desc' ? 'selected' : '' }}>Nama: Z - A</option>
                </select>
                <i data-lucide="chevron-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </div>
        </form>
    </div>

    <!-- Grid Kartu Ruangan (Sesuai Mockup 3 Kolom) -->
    @if($fasilitas->isEmpty())
        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-2xs p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#801414] flex items-center justify-center mx-auto mb-3">
                <i data-lucide="building" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base sm:text-lg font-black text-gray-900">Tidak Ada Ruangan Ditemukan</h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-md mx-auto font-medium">
                Tidak ada ruangan yang cocok dengan kriteria pencarian Anda. Silakan coba kata kunci atau filter lain.
            </p>
            <div class="mt-4">
                <a href="{{ route('katalog.ruang') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#801414] text-white text-xs font-bold rounded-xl shadow-xs">
                    Reset Filter
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @foreach($fasilitas as $f)
                @php
                    $isBorrowed = in_array($f->id, $dipinjamIds);
                    
                    // Deteksi gambar representatif
                    $imgUrl = null;
                    if ($f->gambar && file_exists(public_path($f->gambar))) {
                        $imgUrl = asset($f->gambar);
                    } elseif ($f->gambar && file_exists(public_path('storage/' . $f->gambar))) {
                        $imgUrl = asset('storage/' . $f->gambar);
                    } else {
                        // Fallback cerdas berdasarkan nama ruangan
                        $namaLower = strtolower($f->nama_fasilitas);
                        if (str_contains($namaLower, 'aula')) {
                            $imgUrl = asset('images/aula-fst.jpg');
                        } elseif (str_contains($namaLower, 'lab') || str_contains($namaLower, 'komputer')) {
                            $imgUrl = asset('images/lab-komputer.jpg');
                        } else {
                            $imgUrl = asset('images/ruang-kelas.jpg');
                        }
                    }

                    // Kapasitas formatted
                    $kapasitasStr = $f->kapasitas ?: '40 orang';
                    if (!str_contains(strtolower($kapasitasStr), 'orang')) {
                        $kapasitasStr .= ' orang';
                    }

                    // Jenis kategori
                    $jenisStr = $f->kategori ? $f->kategori->nama_kategori : 'Ruang Kelas';

                    // Fasilitas pendukung (Infocus, AC, dll)
                    $fasilitasStr = $f->deskripsi ?: 'Infocus, AC, Papan Tulis';

                    $modalPayload = [
                        'id' => $f->id,
                        'nama' => $f->nama_fasilitas,
                        'kode' => $f->kode_fasilitas,
                        'kategori' => $jenisStr,
                        'kapasitas' => $kapasitasStr,
                        'fasilitas' => $fasilitasStr,
                        'gambar' => $imgUrl,
                        'status' => $isBorrowed ? 'Sedang Dipinjam' : ($f->status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia'),
                        'is_available' => !$isBorrowed && $f->status === 'tersedia',
                        'peminjaman_url' => route('peminjaman.create') . '?fasilitas=' . $f->id,
                    ];
                @endphp

                <!-- Card Ruangan (Sesuai Mockup) -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs hover:shadow-md transition-all duration-200 p-3.5 sm:p-4 flex flex-col justify-between group">
                    <div>
                        <!-- Foto Ruangan + Badge Status -->
                        <div class="relative rounded-2xl overflow-hidden aspect-video bg-gray-100">
                            <img src="{{ $imgUrl }}" 
                                 alt="{{ $f->nama_fasilitas }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Status Badge Floating -->
                            <div class="absolute top-3 right-3">
                                @if($isBorrowed)
                                    <span class="px-2.5 py-1 bg-white/95 backdrop-blur-xs text-amber-700 text-[11px] font-bold rounded-full shadow-sm flex items-center gap-1.5 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span>Dipinjam</span>
                                    </span>
                                @elseif($f->status === 'tersedia')
                                    <span class="px-2.5 py-1 bg-white/95 backdrop-blur-xs text-emerald-700 text-[11px] font-bold rounded-full shadow-sm flex items-center gap-1.5 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span>Tersedia</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-white/95 backdrop-blur-xs text-rose-700 text-[11px] font-bold rounded-full shadow-sm flex items-center gap-1.5 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span>Tidak Tersedia</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Info Ruangan -->
                        <div class="pt-3.5 space-y-2">
                            <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight leading-snug">
                                {{ $f->nama_fasilitas }}
                            </h3>

                            <div class="space-y-1.5 text-xs sm:text-[13px] text-gray-700 font-medium">
                                <!-- Row 1: Kapasitas -->
                                <div class="flex items-center gap-2">
                                    <i data-lucide="users" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                    <span>Kapasitas {{ $kapasitasStr }}</span>
                                </div>

                                <!-- Row 2: Jenis -->
                                <div class="flex items-center gap-2">
                                    <i data-lucide="door-open" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                    <span>Jenis: {{ $jenisStr }}</span>
                                </div>

                                <!-- Row 3: Fasilitas -->
                                <div class="flex items-center gap-2">
                                    <i data-lucide="sparkles" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                    <span class="truncate">Fasilitas: {{ $fasilitasStr }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi: Lihat Detail & Ajukan Pinjam -->
                    <div class="pt-4 mt-2 flex items-center justify-between gap-2">
                        <a href="{{ route('katalog.ruang.detail', $f->id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 active:scale-[0.98] text-gray-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                            <span>Detail</span>
                            <span>→</span>
                        </a>
                        @if($f->status === 'tersedia' && !$isBorrowed)
                            <a href="{{ route('katalog.ruang.detail', $f->id) }}?ajukan=1" 
                               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs font-bold rounded-xl shadow-2xs transition-colors cursor-pointer">
                                <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                                <span>Ajukan Pinjam</span>
                            </a>
                        @else
                            <span class="inline-flex items-center px-2.5 py-2 bg-gray-100 text-gray-400 text-xs font-bold rounded-xl cursor-not-allowed">
                                Tidak Tersedia
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
