@extends('layouts.app')

@section('title', 'Katalog Alat - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="{ showDetailModal: false, selectedTool: null }">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Katalog Alat
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Lihat dan pilih alat yang tersedia untuk dipinjam.
        </p>
    </div>

    <!-- Filter Bar (Sesuai Mockup) -->
    <form id="filterForm" method="GET" action="{{ route('katalog.alat') }}" 
          class="bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center gap-3">
        
        @if(request('urutkan'))
            <input type="hidden" name="urutkan" value="{{ request('urutkan') }}">
        @endif

        <!-- Input 1: Cari nama alat -->
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input type="text" 
                   name="cari" 
                   value="{{ request('cari') }}" 
                   placeholder="Cari nama alat" 
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

        <!-- Tombol Cari & Reset -->
        <div class="flex items-center gap-2 shrink-0">
            <button type="submit" 
                    class="w-full md:w-auto px-6 py-2.5 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Cari</span>
            </button>

            @if(request('cari') || request('kategori') || request('tanggal') || (request('urutkan') && request('urutkan') !== 'terbaru'))
                <a href="{{ route('katalog.alat') }}" 
                   class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl sm:rounded-2xl transition-all flex items-center justify-center" 
                   title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Sub-Header: Daftar Alat & Urutkan (Sesuai Mockup) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <div class="flex items-center flex-wrap gap-2.5">
            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                Daftar Alat
            </h2>
            <span class="px-2.5 py-0.5 rounded-full bg-[#FEE2E2] text-[#801414] text-xs font-black">
                {{ $fasilitas->count() }} Alat
            </span>

            @if(request('kategori'))
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs font-bold border border-gray-200">
                    <span>Jenis: {{ request('kategori') }}</span>
                    <a href="{{ route('katalog.alat', array_merge(request()->except('kategori'))) }}" class="text-gray-400 hover:text-gray-700">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </a>
                </span>
            @endif
        </div>

        <!-- Dropdown Urutkan -->
        <form method="GET" action="{{ route('katalog.alat') }}" class="flex items-center gap-2" id="sortForm">
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

    <!-- Grid Kartu Alat (Sesuai Mockup 3 Kolom) -->
    @if($fasilitas->isEmpty())
        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-2xs p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#801414] flex items-center justify-center mx-auto mb-3">
                <i data-lucide="monitor" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base sm:text-lg font-black text-gray-900">Tidak Ada Alat Ditemukan</h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-md mx-auto font-medium">
                Tidak ada alat yang cocok dengan kriteria pencarian Anda. Silakan coba kata kunci atau filter lain.
            </p>
            <div class="mt-4">
                <a href="{{ route('katalog.alat') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#801414] text-white text-xs font-bold rounded-xl shadow-xs">
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
                        // Fallback cerdas berdasarkan nama alat
                        $namaLower = strtolower($f->nama_fasilitas);
                        if (str_contains($namaLower, 'infocus') || str_contains($namaLower, 'proyektor') || str_contains($namaLower, 'lcd')) {
                            $imgUrl = asset('images/infocus.jpg');
                        } elseif (str_contains($namaLower, 'sound') || str_contains($namaLower, 'speaker')) {
                            $imgUrl = asset('images/sound-system.jpg');
                        } elseif (str_contains($namaLower, 'mikrofon') || str_contains($namaLower, 'mic')) {
                            $imgUrl = asset('images/mikrofon.jpg');
                        } else {
                            $imgUrl = asset('images/infocus.jpg');
                        }
                    }

                    // Format jumlah
                    $jumlahStr = $f->jumlah ?: '1 Unit';

                    // Kategori
                    $kategoriStr = $f->kategori ? $f->kategori->nama_kategori : 'Peralatan';

                    // Kondisi
                    $kondisiStr = ucfirst(str_replace('_', ' ', $f->kondisi_awal ?: 'Baik'));

                    $modalPayload = [
                        'id' => $f->id,
                        'nama' => $f->nama_fasilitas,
                        'kode' => $f->kode_fasilitas,
                        'kategori' => $kategoriStr,
                        'kondisi' => $kondisiStr,
                        'jumlah' => $jumlahStr,
                        'deskripsi' => $f->deskripsi ?: 'Tidak ada deskripsi tambahan untuk alat ini.',
                        'gambar' => $imgUrl,
                        'status' => $isBorrowed ? 'Sedang Dipinjam' : ($f->status === 'tersedia' ? 'Tersedia' : ucfirst(str_replace('_', ' ', $f->status))),
                        'isBorrowed' => $isBorrowed,
                    ];
                @endphp

                <!-- Kartu Alat Sesuai Mockup -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs overflow-hidden hover:shadow-md transition-all duration-200 flex flex-col group">
                    
                    <!-- Container Gambar & Badge Status -->
                    <div class="h-44 sm:h-48 relative overflow-hidden bg-white sm:bg-gray-50/50 flex items-center justify-center p-3 border-b border-gray-100">
                        <img src="{{ $imgUrl }}" 
                             alt="{{ $f->nama_fasilitas }}" 
                             class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">

                        <!-- Badge Status (Kanan Atas Sesuai Mockup) -->
                        <div class="absolute top-3 right-3 z-10">
                            @if($isBorrowed)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 backdrop-blur-xs text-yellow-700 text-xs font-bold rounded-full border border-yellow-200 shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                                    <span>Sedang Dipinjam</span>
                                </span>
                            @elseif($f->status === 'tersedia')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 backdrop-blur-xs text-emerald-700 text-xs font-bold rounded-full border border-emerald-200 shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Tersedia</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 backdrop-blur-xs text-red-700 text-xs font-bold rounded-full border border-red-200 shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    <span>{{ ucfirst(str_replace('_', ' ', $f->status)) }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Alat (Sesuai Mockup) -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Judul Alat -->
                            <h3 class="font-bold text-gray-900 text-base sm:text-lg mb-3 tracking-tight">
                                {{ $f->nama_fasilitas }}
                            </h3>

                            <!-- Spesifikasi Detail 3 Baris Sesuai Mockup -->
                            <div class="space-y-1.5 text-xs text-gray-700">
                                <!-- Baris 1: Kondisi -->
                                <div class="flex items-center">
                                    <div class="flex items-center gap-2 w-36 shrink-0 text-gray-800 font-medium">
                                        <i data-lucide="settings" class="w-4 h-4 text-gray-700"></i>
                                        <span>Kondisi</span>
                                    </div>
                                    <span class="text-gray-400 mr-2 font-medium">:</span>
                                    <span class="font-bold text-gray-900">{{ $kondisiStr }}</span>
                                </div>

                                <!-- Baris 2: Kode Alat (Unik untuk Pemantauan) -->
                                <div class="flex items-center">
                                    <div class="flex items-center gap-2 w-36 shrink-0 text-gray-800 font-medium">
                                        <i data-lucide="barcode" class="w-4 h-4 text-gray-700"></i>
                                        <span>Kode Alat</span>
                                    </div>
                                    <span class="text-gray-400 mr-2 font-medium">:</span>
                                    <span class="font-bold text-gray-900 font-mono tracking-wide">{{ $f->kode_fasilitas }}</span>
                                </div>

                                <!-- Baris 3: Kategori -->
                                <div class="flex items-center">
                                    <div class="flex items-center gap-2 w-36 shrink-0 text-gray-800 font-medium">
                                        <i data-lucide="tag" class="w-4 h-4 text-gray-700"></i>
                                        <span>Kategori</span>
                                    </div>
                                    <span class="text-gray-400 mr-2 font-medium">:</span>
                                    <span class="font-bold text-gray-900">{{ $kategoriStr }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi: Lihat Detail -> (Sesuai Mockup) -->
                        <div class="mt-5 pt-1">
                            <button @click="selectedTool = {{ json_encode($modalPayload) }}; showDetailModal = true"
                                    type="button"
                                    class="w-full py-2.5 px-4 bg-[#FEE2E2] hover:bg-[#fecaca] active:scale-[0.98] text-[#DC2626] text-xs sm:text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Lihat Detail</span>
                                <span class="text-sm font-black">&rarr;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal Interaktif Detail Alat -->
    <div x-show="showDetailModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
         @keydown.escape.window="showDetailModal = false">
        
        <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-gray-100 transform transition-all"
             @click.away="showDetailModal = false"
             x-show="showDetailModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-red-100 text-[#801414] flex items-center justify-center">
                        <i data-lucide="monitor" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900" x-text="selectedTool?.nama"></h3>
                        <p class="text-[11px] text-gray-500 font-medium" x-text="'Kode: ' + (selectedTool?.kode || '-')"></p>
                    </div>
                </div>
                <button @click="showDetailModal = false" class="w-8 h-8 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-all cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                <!-- Preview Gambar -->
                <div class="h-44 rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden flex items-center justify-center p-2 relative">
                    <img :src="selectedTool?.gambar" :alt="selectedTool?.nama" class="w-full h-full object-contain">
                    <div class="absolute top-2.5 right-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 text-xs font-bold rounded-full border shadow-2xs"
                              :class="selectedTool?.status === 'Tersedia' ? 'text-emerald-700 border-emerald-200' : 'text-amber-700 border-amber-200'">
                            <span class="w-2 h-2 rounded-full" :class="selectedTool?.status === 'Tersedia' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="selectedTool?.status"></span>
                        </span>
                    </div>
                </div>

                <!-- Spesifikasi Ringkas -->
                <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 space-y-2.5 text-xs sm:text-sm">
                    <div class="flex justify-between items-center py-1 border-b border-gray-200/50">
                        <span class="text-gray-500 font-medium">Kondisi Alat</span>
                        <span class="font-bold text-gray-900" x-text="selectedTool?.kondisi"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-200/50">
                        <span class="text-gray-500 font-medium">Kode Alat (ID Unik)</span>
                        <span class="font-bold text-gray-900 font-mono bg-gray-100 px-2 py-0.5 rounded border border-gray-200" x-text="selectedTool?.kode"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-200/50">
                        <span class="text-gray-500 font-medium">Kategori Fasilitas</span>
                        <span class="font-bold text-gray-900" x-text="selectedTool?.kategori"></span>
                    </div>
                    <div class="pt-1">
                        <span class="text-gray-500 font-medium block mb-1">Deskripsi / Kelengkapan:</span>
                        <p class="text-gray-700 text-xs leading-relaxed" x-text="selectedTool?.deskripsi"></p>
                    </div>
                </div>

                <!-- Banner Informasi Loket -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-amber-700 shrink-0 mt-0.5"></i>
                    <div class="text-xs text-amber-900">
                        <p class="font-bold mb-0.5">Tata Cara Peminjaman Alat</p>
                        <p class="leading-relaxed">Peminjaman peralatan dilakukan secara langsung di loket Pengelola / Laboran FST dengan membawa KTM atau kartu identitas resmi yang masih berlaku.</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-end gap-3">
                <button @click="showDetailModal = false" 
                        type="button" 
                        class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-xs sm:text-sm font-bold text-gray-700 transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
