@extends('layouts.guest')

@section('title', 'Beranda - SIMORA FST')

@push('styles')
<style>
    /* Hide ugly scrollbars on mobile while keeping smooth touch scrolling */
    .no-scrollbar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    .no-scrollbar {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="w-full bg-marun-900 text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                Sistem Manajemen Peminjaman Ruang & Alat Terpadu
            </h1>
            <p class="text-lg md:text-xl text-marun-100 mb-8">
                Fakultas Sains dan Teknologi UIN Imam Bonjol Padang. Cek ketersediaan dan ajukan peminjaman dengan mudah.
            </p>
            
            <!-- Live Search -->
            <div x-data="{ query: '', results: [], show: false, search() {
                if (this.query.length < 2) { this.results = []; this.show = false; return; }
                fetch('/api/fasilitas/cari?q=' + this.query)
                    .then(res => res.json())
                    .then(data => { this.results = data; this.show = true; });
            }}" class="relative max-w-xl mx-auto">
                <div class="relative flex items-center">
                    <i data-lucide="search" class="absolute left-4 w-5 h-5 text-gray-400"></i>
                    <input type="text" x-model="query" @input.debounce.300ms="search" @click.away="show = false" @focus="search"
                        class="w-full pl-12 pr-4 py-4 rounded-full text-gray-900 focus:outline-none focus:ring-4 focus:ring-mustard-500/50 shadow-lg text-lg"
                        placeholder="Cari ruang atau alat...">
                    <button class="absolute right-2 px-6 py-2 bg-mustard-500 hover:bg-mustard-400 text-marun-950 font-bold rounded-full transition-colors">
                        Cari
                    </button>
                </div>
                
                <!-- Search Results Dropdown -->
                <div x-show="show" x-transition style="display: none;" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden text-left z-50">
                    <template x-if="results.length === 0 && query.length >= 2">
                        <div class="p-4 text-gray-500 text-center">Tidak ada hasil ditemukan.</div>
                    </template>
                    <ul class="max-h-80 overflow-y-auto">
                        <template x-for="item in results" :key="item.id">
                            <li class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                                <a href="#kalender" @click="show = false; $dispatch('filter-calendar', {id: item.id})" class="flex items-start p-4 gap-4">
                                    <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center shrink-0">
                                        <i :data-lucide="item.kategori.jenis === 'ruangan' ? 'door-open' : 'monitor'" class="w-6 h-6 text-gray-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900" x-text="item.nama_fasilitas"></h4>
                                        <div class="flex items-center gap-2 mt-1 text-xs">
                                            <span class="text-gray-500" x-text="item.kode_fasilitas"></span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium"
                                                :class="{
                                                    'bg-green-100 text-green-800': item.status === 'tersedia',
                                                    'bg-blue-100 text-blue-800': item.status === 'dipinjam',
                                                    'bg-red-100 text-red-800': item.status === 'perbaikan'
                                                }" x-text="item.status"></span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Summary -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="pt-2 md:pt-0">
            <p class="text-3xl font-black text-marun-900">{{ $totalRuangan }}</p>
            <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wider">🏢 Total Ruangan</p>
        </div>
        <div class="pt-2 md:pt-0">
            <p class="text-3xl font-black text-marun-900">{{ $totalAlat }}</p>
            <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wider">📽️ Total Alat / Perangkat</p>
        </div>
        <div class="pt-2 md:pt-0">
            <p class="text-3xl font-black text-emerald-600">{{ $fasilitasTersedia }}</p>
            <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wider">🟢 Siap Dipinjam</p>
        </div>
        <div class="pt-2 md:pt-0">
            <p class="text-3xl font-black text-mustard-500">{{ $totalFasilitas }}</p>
            <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wider">📦 Total Fasilitas</p>
        </div>
    </div>
</div>

<!-- Jadwal & Kalender Section -->
<div id="kalender" x-data="jadwalSection()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
    <!-- Header & Switcher Jenis Fasilitas -->
    <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-marun-50 border border-marun-100 text-marun-800 text-xs font-semibold uppercase tracking-wider mb-2">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                Jadwal &amp; Ketersediaan Real-Time
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Ketersediaan Ruangan &amp; Alat</h2>
            <p class="text-gray-500 mt-1 max-w-2xl">Pantau penggunaan fasilitas dan ketersediaan slot secara real-time. Pilih tanggal untuk melihat fasilitas yang kosong atau sedang dipakai.</p>
        </div>
        
        <!-- Toggle Jenis Fasilitas: Ruangan vs Alat -->
        <div class="flex items-center bg-gray-100 p-1 sm:p-1.5 rounded-2xl border border-gray-200 w-full sm:w-auto shadow-inner">
            <button type="button" @click="setJenis('ruangan')" 
                    class="flex-1 sm:flex-initial flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-black transition-all cursor-pointer select-none active:scale-95"
                    :class="jenis === 'ruangan' ? 'bg-marun-800 text-mustard-400 shadow-md ring-2 ring-marun-900/10' : 'text-gray-600 hover:text-gray-900'">
                <i data-lucide="door-open" class="w-4 h-4 shrink-0"></i>
                <span>Ruangan &amp; Gedung</span>
            </button>
            <button type="button" @click="setJenis('peralatan')" 
                    class="flex-1 sm:flex-initial flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-black transition-all cursor-pointer select-none active:scale-95"
                    :class="jenis === 'peralatan' ? 'bg-marun-800 text-mustard-400 shadow-md ring-2 ring-marun-900/10' : 'text-gray-600 hover:text-gray-900'">
                <i data-lucide="cpu" class="w-4 h-4 shrink-0"></i>
                <span>Alat &amp; Perlengkapan</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Navigasi Tanggal, Kategori & Filter Status -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200 shadow-sm mb-5 flex flex-col gap-4">
        <!-- Baris 1: Informasi Tanggal, Navigasi Stepper & Mode Tampilan -->
        <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-3.5 sm:gap-4">
            <!-- Navigasi Tanggal & Stepper -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                <!-- Row Stepper + Datepicker di Mobile -->
                <div class="flex items-center gap-2">
                    <div class="inline-flex rounded-xl shadow-2xs border border-gray-200 bg-gray-50 p-1 shrink-0">
                        <button type="button" @click="changeDate(-1)" title="Hari Sebelumnya"
                                class="p-1.5 sm:p-2 rounded-lg text-gray-600 hover:bg-white hover:text-marun-800 active:scale-90 transition-all cursor-pointer">
                            <i data-lucide="chevron-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </button>
                        <button type="button" @click="today()"
                                class="px-2.5 sm:px-3 py-1 rounded-lg text-xs font-bold text-gray-700 hover:bg-white hover:text-marun-800 active:scale-95 transition-all cursor-pointer whitespace-nowrap"
                                :class="isToday ? 'bg-white text-marun-900 shadow-2xs' : ''">
                            Hari Ini
                        </button>
                        <button type="button" @click="changeDate(1)" title="Hari Berikutnya"
                                class="p-1.5 sm:p-2 rounded-lg text-gray-600 hover:bg-white hover:text-marun-800 active:scale-90 transition-all cursor-pointer">
                            <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </button>
                    </div>

                    <!-- Input Date Picker -->
                    <div class="relative flex-1 sm:flex-initial">
                        <input type="date" x-model="tanggal" @change="fetchMatriks()"
                               class="w-full sm:w-auto border border-gray-200 rounded-xl px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs md:text-sm font-bold text-gray-800 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-marun-700 focus:border-marun-700 cursor-pointer shadow-2xs transition-colors">
                    </div>
                </div>

                <!-- Label Tanggal Aktif -->
                <div class="flex items-center justify-between sm:justify-start gap-2 px-1 sm:pl-2">
                    <span class="text-sm sm:text-base md:text-lg font-black text-marun-950 tracking-tight leading-tight" x-text="data.tanggal_formatted || 'Memuat tanggal...'"></span>
                    <span x-show="isToday" class="sm:hidden px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-100 text-red-800 border border-red-200 shrink-0">Hari Ini</span>
                </div>
            </div>

            <!-- View Mode Switcher Khusus Peralatan (Kartu Status vs Timeline Jam) -->
            <template x-if="jenis === 'peralatan'">
                <div class="w-full lg:w-auto">
                    <div class="grid grid-cols-2 lg:inline-flex bg-gray-100 p-1 rounded-xl border border-gray-200 w-full sm:w-auto">
                        <button type="button" @click="setSubModeAlat('kartu')" 
                                class="px-3 py-2 sm:py-1.5 rounded-lg text-xs font-black transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95"
                                :class="subModeAlat === 'kartu' ? 'bg-marun-800 text-mustard-400 shadow-xs' : 'text-gray-600 hover:text-gray-900'">
                            <i data-lucide="grid-2x2" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>Kartu Status</span>
                        </button>
                        <button type="button" @click="setSubModeAlat('timeline')" 
                                class="px-3 py-2 sm:py-1.5 rounded-lg text-xs font-black transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95"
                                :class="subModeAlat === 'timeline' ? 'bg-marun-800 text-mustard-400 shadow-xs' : 'text-gray-600 hover:text-gray-900'">
                            <i data-lucide="clock" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>Timeline Jam</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Baris 2: Quick Search & Filter Chips (No-Scrollbar, Touch-Friendly) -->
        <div class="pt-3 border-t border-gray-100 flex flex-col gap-3">
            <!-- Pencarian Cepat & Filter Kategori -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2.5">
                <!-- Live Search Box Interaktif -->
                <div class="relative w-full md:w-64 shrink-0">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" x-model="searchKeyword" placeholder="Cari nama atau kode..."
                           class="w-full pl-9 pr-8 py-1.5 sm:py-2 text-xs font-medium rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-marun-700 focus:border-marun-700 transition-colors shadow-2xs">
                    <button x-show="searchKeyword" @click="searchKeyword = ''" type="button"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-200">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Chips Filter Kategori (No Scrollbar, Touch Scroll) -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth py-0.5 flex-1 w-full"
                     style="-ms-overflow-style: none; scrollbar-width: none;">
                    <span class="text-xs font-bold text-gray-500 whitespace-nowrap mr-1 hidden sm:inline">Kategori:</span>
                    <button type="button" @click="setKategori('semua')"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition-all whitespace-nowrap active:scale-95 cursor-pointer shrink-0"
                            :class="kategori === 'semua' ? 'bg-marun-800 text-mustard-400 shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        Semua Kategori
                    </button>
                    <template x-for="kat in data.kategori_list" :key="kat">
                        <button type="button" @click="setKategori(kat)"
                                class="px-3 py-1.5 rounded-full text-xs font-bold transition-all whitespace-nowrap active:scale-95 cursor-pointer shrink-0"
                                :class="kategori === kat ? 'bg-marun-800 text-mustard-400 shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                x-text="kat">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Filter Status Ketersediaan (Khusus Peralatan) -->
            <template x-if="jenis === 'peralatan'">
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth py-0.5 w-full text-xs"
                     style="-ms-overflow-style: none; scrollbar-width: none;">
                    <span class="text-xs font-bold text-gray-500 whitespace-nowrap mr-1">Status:</span>
                    <button type="button" @click="setFilterStatus('semua')"
                            class="px-2.5 py-1 rounded-lg font-bold transition-all whitespace-nowrap active:scale-95 cursor-pointer shrink-0"
                            :class="filterStatus === 'semua' ? 'bg-gray-900 text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        Semua
                    </button>
                    <button type="button" @click="setFilterStatus('tersedia')"
                            class="px-2.5 py-1 rounded-lg font-bold transition-all whitespace-nowrap flex items-center gap-1.5 active:scale-95 cursor-pointer shrink-0"
                            :class="filterStatus === 'tersedia' ? 'bg-emerald-700 text-white shadow-2xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100'">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Tersedia (Kosong)
                    </button>
                    <button type="button" @click="setFilterStatus('terpakai')"
                            class="px-2.5 py-1 rounded-lg font-bold transition-all whitespace-nowrap flex items-center gap-1.5 active:scale-95 cursor-pointer shrink-0"
                            :class="filterStatus === 'terpakai' ? 'bg-red-700 text-white shadow-2xs' : 'bg-red-50 text-red-800 hover:bg-red-100'">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        Sedang Dipakai
                    </button>
                    <button type="button" @click="setFilterStatus('perbaikan')"
                            class="px-2.5 py-1 rounded-lg font-bold transition-all whitespace-nowrap flex items-center gap-1.5 active:scale-95 cursor-pointer shrink-0"
                            :class="filterStatus === 'perbaikan' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100'">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Rusak / Perbaikan
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Info Ringkasan & Legend -->
    <div class="mb-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs bg-gray-50/80 px-3.5 py-2.5 rounded-xl border border-gray-200/80">
        <!-- Quick Stat Badges -->
        <div class="flex items-center gap-2 font-semibold text-gray-700 flex-wrap">
            <span class="bg-white px-2.5 py-1 rounded-lg border border-gray-200 shadow-2xs">
                Total: <strong class="text-gray-900" x-text="filteredMatriks.length"></strong> <span x-text="jenis === 'ruangan' ? 'Ruangan' : 'Alat'"></span>
            </span>
            <span class="text-emerald-700 bg-emerald-100/70 border border-emerald-200 px-2.5 py-1 rounded-lg inline-flex items-center gap-1.5 font-bold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span><strong x-text="data.kosong_hari_ini || 0"></strong> Tersedia Bebas</span>
            </span>
            <span class="text-red-700 bg-red-100/70 border border-red-200 px-2.5 py-1 rounded-lg inline-flex items-center gap-1.5 font-bold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-red-600"></span>
                <span><strong x-text="data.terpakai_hari_ini || 0"></strong> Sedang Dipakai</span>
            </span>
            <template x-if="jenis === 'peralatan' && data.rusak_hari_ini > 0">
                <span class="text-amber-700 bg-amber-100/70 border border-amber-200 px-2.5 py-1 rounded-lg inline-flex items-center gap-1.5 font-bold shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span><strong x-text="data.rusak_hari_ini || 0"></strong> Rusak</span>
                </span>
            </template>
        </div>

        <!-- Legend Warna -->
        <div class="flex items-center gap-3 font-semibold text-gray-500 text-[11px] self-end sm:self-auto">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-2.5 rounded bg-emerald-50 border border-emerald-400"></span>
                <span>Tersedia</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-2.5 rounded bg-gradient-to-r from-red-600 to-marun-900 shadow-2xs"></span>
                <span>Dipinjam</span>
            </div>
            <template x-if="jenis === 'ruangan'">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-2.5 rounded bg-gradient-to-r from-amber-500 to-amber-600 shadow-2xs"></span>
                    <span>Menunggu</span>
                </div>
            </template>
        </div>
    </div>

    <!-- TAMPILAN 1: JIKA PERALATAN & SUB-MODE KARTU STATUS (GRID CARD VIEW - DEFAULT UNTUK ALAT) -->
    <div x-show="jenis === 'peralatan' && subModeAlat === 'kartu'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        
        <!-- Loading State -->
        <div x-show="loading" class="py-16 text-center text-gray-500 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <i data-lucide="loader-2" class="w-7 h-7 animate-spin text-mustard-500 mx-auto mb-2"></i>
            <p class="font-bold text-gray-700 text-sm">Memuat daftar peralatan &amp; kondisi...</p>
        </div>

        <!-- Grid Kartu Alat -->
        <div x-show="!loading && filteredMatriks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="item in filteredMatriks" :key="item.id">
                <div class="bg-white border rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between"
                     :class="item.has_booking ? 'border-red-200 ring-1 ring-red-100' : (item.kondisi === 'rusak_ringan' ? 'border-amber-200' : 'border-gray-200')">
                    
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Header Kartu: Icon Kategori & Badges -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-marun-50 text-marun-800 flex items-center justify-center shrink-0 shadow-2xs">
                                    <template x-if="item.kategori.toLowerCase().includes('infocus') || item.kategori.toLowerCase().includes('proyektor')">
                                        <i data-lucide="projector" class="w-5 h-5"></i>
                                    </template>
                                    <template x-if="item.kategori.toLowerCase().includes('audio') || item.kategori.toLowerCase().includes('speaker')">
                                        <i data-lucide="volume-2" class="w-5 h-5"></i>
                                    </template>
                                    <template x-if="item.kategori.toLowerCase().includes('kabel') || item.kategori.toLowerCase().includes('konverter')">
                                        <i data-lucide="cable" class="w-5 h-5"></i>
                                    </template>
                                    <template x-if="item.kategori.toLowerCase().includes('pointer')">
                                        <i data-lucide="mouse-pointer-click" class="w-5 h-5"></i>
                                    </template>
                                    <template x-if="!item.kategori.toLowerCase().includes('infocus') && !item.kategori.toLowerCase().includes('proyektor') && !item.kategori.toLowerCase().includes('audio') && !item.kategori.toLowerCase().includes('speaker') && !item.kategori.toLowerCase().includes('kabel') && !item.kategori.toLowerCase().includes('pointer')">
                                        <i data-lucide="cpu" class="w-5 h-5"></i>
                                    </template>
                                </div>

                                <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                    <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded border border-gray-200/60" x-text="item.kode_fasilitas"></span>
                                    <span class="text-xs font-semibold text-marun-800 bg-marun-50 px-2.5 py-0.5 rounded-full" x-text="item.kategori"></span>
                                </div>
                            </div>

                            <!-- Nama Fasilitas / Alat -->
                            <h3 class="font-black text-gray-900 text-base leading-snug" x-text="item.nama_fasilitas"></h3>
                            
                            <!-- Deskripsi & Kelengkapan -->
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2" x-text="item.deskripsi || 'Kelengkapan standar operasional.'"></p>

                            <!-- Indikator Kondisi Fisik Alat -->
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                                <span class="text-gray-500 font-semibold">Kondisi Fisik:</span>
                                <div>
                                    <template x-if="item.kondisi === 'baik'">
                                        <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Baik (Normal 100%)
                                        </span>
                                    </template>
                                    <template x-if="item.kondisi === 'rusak_ringan'">
                                        <span class="inline-flex items-center gap-1 font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full" title="Bisa digunakan dengan catatan kelengkapan">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Rusak Ringan
                                        </span>
                                    </template>
                                    <template x-if="item.kondisi === 'rusak_berat' || item.status_fisik === 'perbaikan'">
                                        <span class="inline-flex items-center gap-1 font-bold text-red-700 bg-red-50 border border-red-200 px-2.5 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Perbaikan / Servis
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Status Ketersediaan pada Tanggal Terpilih -->
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <!-- Case 1: Tersedia Bebas Hari Ini -->
                            <template x-if="item.tersedia_hari_ini">
                                <div class="bg-emerald-50/80 border border-emerald-200/60 rounded-xl p-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                        <div>
                                            <p class="text-xs font-black text-emerald-900 leading-tight">Tersedia Hari Ini</p>
                                            <p class="text-[10px] text-emerald-700 font-medium">Bebas &amp; siap dipinjam</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('katalog.alat') }}" 
                                       class="text-xs font-bold text-white bg-marun-800 hover:bg-marun-700 px-3 py-1.5 rounded-lg shadow-xs transition-colors shrink-0">
                                        Lihat Alat
                                    </a>
                                </div>
                            </template>

                            <!-- Case 2: Sedang Dipakai Hari Ini -->
                            <template x-if="item.has_booking">
                                <div class="bg-red-50/90 border border-red-200/80 rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-black text-red-900 flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                                            Sedang Dipakai Hari Ini
                                        </span>
                                        <span class="text-[10px] font-bold text-red-700 bg-red-100 px-1.5 py-0.2 rounded" x-text="item.peminjaman_list.length + ' Peminjaman'"></span>
                                    </div>
                                    <!-- List Peminjaman yang Sedang Berjalan -->
                                    <div class="space-y-1.5">
                                        <template x-for="b in item.peminjaman_list" :key="b.id">
                                            <div @click="openDetail(b)"
                                                 class="bg-white p-2 sm:p-2.5 rounded-lg border border-red-100 shadow-2xs hover:bg-red-50/60 active:scale-[0.98] cursor-pointer transition-all flex items-center justify-between text-xs"
                                                 :title="'Klik untuk rincian peminjaman ' + b.peminjam">
                                                <div class="min-w-0 pr-2">
                                                    <p class="font-bold text-gray-900 truncate" x-text="b.peminjam"></p>
                                                    <p class="text-[10px] text-gray-500 truncate" x-text="b.tujuan"></p>
                                                </div>
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <span class="text-[10px] font-black font-mono text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded whitespace-nowrap"
                                                          x-text="b.label_waktu"></span>
                                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-red-400"></i>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Case 3: Dalam Pemeliharaan / Rusak -->
                            <template x-if="!item.has_booking && !item.tersedia_hari_ini">
                                <div class="bg-gray-100 rounded-xl p-3 flex items-center gap-2 text-xs text-gray-600">
                                    <i data-lucide="wrench" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                    <span>Alat sedang dalam pemeliharaan teknis.</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State Filter Alat -->
        <div x-show="!loading && filteredMatriks.length === 0" class="py-16 text-center text-gray-500 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
            <p class="font-bold text-gray-700">Tidak ada alat yang sesuai dengan pencarian / filter ini.</p>
            <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci atau pilih kategori / status lain.</p>
        </div>
    </div>

    <!-- TAMPILAN 2: MATRIKS TIMELINE GANTT BAR PER JAM (Untuk Ruangan ATAU Alat Mode Timeline) -->
    <div x-show="jenis === 'ruangan' || subModeAlat === 'timeline'" 
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        
        <!-- Container Tabel Matriks Timeline (Scrollable Gantt Chart) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
            
            <!-- Loading Indicator -->
            <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-40 transition-opacity">
                <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-xl shadow-lg border border-gray-100 text-marun-900 text-sm font-bold">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-mustard-500"></i>
                    Memuat grafik jadwal...
                </div>
            </div>

            <!-- Mobile Gesture & Quick Center Bar -->
            <div class="sm:hidden flex items-center justify-between px-3.5 py-2 bg-gradient-to-r from-marun-950 to-marun-900 text-white text-[11px] font-medium border-b border-marun-800">
                <div class="flex items-center gap-1.5 text-marun-100">
                    <i data-lucide="arrow-left-right" class="w-3.5 h-3.5 text-mustard-400 animate-pulse"></i>
                    <span>Geser layar untuk jam</span>
                </div>
                <button type="button" @click="scrollToCurrentTime()" x-show="isToday && isOperatingHours"
                        class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-500 text-white px-2.5 py-0.5 rounded-md text-[10px] font-bold active:scale-95 transition-all shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                    <span>Jam Kini</span>
                </button>
            </div>

            <div class="overflow-x-auto max-h-[680px] relative scroll-smooth no-scrollbar sm:overflow-x-auto" x-ref="timelineScroll">
                <table class="min-w-full text-left border-collapse table-fixed">
                    <!-- Sticky Header: Baris Skala Waktu (07:00 - 21:00) -->
                    <thead class="sticky top-0 z-30 bg-gray-100/95 backdrop-blur-xs shadow-xs">
                        <tr>
                            <!-- Kolom Fasilitas Sticky (Lebar responsif: 145px di HP agar grafik jam tetap lega) -->
                            <th class="sticky left-0 z-40 bg-gray-100/95 px-2.5 sm:px-4 py-3 sm:py-3.5 text-[11px] sm:text-xs font-extrabold text-marun-950 uppercase tracking-wider border-b border-r border-gray-200 w-36 sm:w-64 min-w-[145px] sm:min-w-[260px] max-w-[145px] sm:max-w-[260px]">
                                <span x-text="jenis === 'ruangan' ? 'Ruangan' : 'Alat'"></span>
                            </th>

                            <!-- Area Header Timeline (Ruler 07:00 - 21:00) -->
                            <th class="p-0 border-b border-gray-200 min-w-[1260px] relative select-none h-12">
                                
                                <!-- Grid 14 Kolom Background untuk Divider Lines & Active Slot -->
                                <div class="w-full h-full divide-x divide-gray-200/80 relative"
                                     style="display: grid; grid-template-columns: repeat(14, minmax(0, 1fr));">
                                    <template x-for="slot in data.slot_jam" :key="slot.mulai">
                                        <div class="h-full transition-colors relative"
                                             :class="isToday && isCurrentSlot(slot) ? 'bg-red-500/10' : ''">
                                        </div>
                                    </template>
                                </div>

                                <!-- 15 Titik Jam Ruler (07:00, 08:00, ..., 21:00) Tepat di Garis Vertikal (Single, Tanpa Dobel Jam) -->
                                <template x-for="(jam, i) in rulerHours" :key="jam">
                                    <div class="absolute top-1.5 pointer-events-none z-10"
                                         :style="`left: ${(i / 14) * 100}%;`"
                                         :class="i === 0 ? 'translate-x-0 pl-1.5' : (i === 14 ? '-translate-x-full pr-1.5' : '-translate-x-1/2')">
                                        <span class="text-xs font-black tracking-tight"
                                              :class="isCurrentHourMark(jam) ? 'text-red-700 font-black' : 'text-marun-950'"
                                              x-text="jam"></span>
                                    </div>
                                </template>

                                <!-- Current Time Marker Pin di Header -->
                                <template x-if="isToday && isOperatingHours">
                                    <div class="absolute top-0 bottom-0 z-40 pointer-events-none -translate-x-1/2 flex flex-col items-center"
                                         :style="`left: ${nowPercent}%;`">
                                        <!-- Floating Pill Badge: Hanya Jam dan Menit (misal: 17:09) -->
                                        <div class="inline-flex items-center gap-1 bg-red-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-md border border-white whitespace-nowrap mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            <span class="font-mono font-bold tracking-tight" x-text="nowTimeFormatted"></span>
                                        </div>
                                        <!-- Arrow pointer down tepat ke garis merah -->
                                        <div class="w-0 h-0 border-l-[3.5px] border-l-transparent border-r-[3.5px] border-r-transparent border-t-[4px] border-t-rose-600"></div>
                                        <!-- Vertical line melalui header -->
                                        <div class="w-0.5 flex-1 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.85)]"></div>
                                    </div>
                                </template>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in filteredMatriks" :key="item.id">
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <!-- Sticky Fasilitas Column (Responsif Mobile) -->
                                <td class="sticky left-0 z-20 bg-white px-2.5 sm:px-4 py-2.5 sm:py-3 border-r border-gray-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)] align-middle w-36 sm:w-64 min-w-[145px] sm:min-w-[260px] max-w-[145px] sm:max-w-[260px]">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-xl bg-marun-50 text-marun-800 flex items-center justify-center shrink-0 shadow-2xs">
                                            <template x-if="jenis === 'ruangan'">
                                                <i data-lucide="door-open" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                                            </template>
                                            <template x-if="jenis === 'peralatan'">
                                                <i data-lucide="cpu" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                                            </template>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-extrabold text-gray-900 text-xs sm:text-sm truncate" :title="item.nama_fasilitas" x-text="item.nama_fasilitas"></p>
                                            <div class="flex items-center gap-1 mt-0.5 sm:mt-1 flex-wrap">
                                                <span class="text-[9px] sm:text-[10px] text-gray-500 font-mono px-1 py-0.2 bg-gray-50 rounded border border-gray-100" x-text="item.kode_fasilitas"></span>
                                                <span class="text-[9px] sm:text-[10px] px-1 py-0.2 rounded font-semibold bg-marun-50 text-marun-800 truncate max-w-[85px] sm:max-w-none" x-text="item.kategori"></span>
                                            </div>
                                            <div class="mt-1 flex items-center gap-1">
                                                <template x-if="item.has_booking">
                                                    <span class="text-[9px] sm:text-[10px] font-bold text-red-700 bg-red-50 border border-red-200/60 px-1.5 py-0.2 rounded-full inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span>
                                                        <span x-text="item.peminjaman_list.length + ' Pinjam'"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!item.has_booking">
                                                    <span class="text-[9px] sm:text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.2 rounded-full inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        <span>Tersedia</span>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Timeline Track Column (Grafik Batang) -->
                                <td class="p-0 align-middle min-w-[1260px] relative overflow-hidden"
                                    :style="`height: ${item.row_height_px || 60}px;`">
                                    
                                    <!-- Background Grid Lines (14 jam) dengan Sorotan Jam Sekarang -->
                                    <div class="absolute inset-0 w-full h-full pointer-events-none divide-x divide-gray-100"
                                         style="display: grid; grid-template-columns: repeat(14, minmax(0, 1fr));">
                                        <template x-for="slot in data.slot_jam" :key="slot.mulai">
                                            <div class="h-full transition-colors"
                                                 :class="isToday && isCurrentSlot(slot) ? 'bg-red-500/5' : 'hover:bg-emerald-50/20'"></div>
                                        </template>
                                    </div>

                                    <!-- Indikator Jalur Kosong (Tersedia Sepanjang Hari) -->
                                    <template x-if="!item.has_booking">
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50/70 border border-emerald-200/50 text-emerald-800 text-[11px] font-semibold">
                                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                                <span>Kosong &amp; tersedia sepanjang hari (07:00 - 21:00)</span>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Grafik Batang Peminjaman (Gantt Bar) -->
                                    <template x-for="booking in item.peminjaman_list" :key="booking.id">
                                        <div @click="openDetail(booking)"
                                             class="absolute z-10 rounded-xl px-3 py-1 flex items-center justify-between shadow-xs hover:shadow-lg transition-all duration-150 cursor-pointer overflow-hidden group select-none hover:scale-[1.008]"
                                             :style="`left: ${booking.left_percent}%; width: ${booking.width_percent}%; top: ${booking.top_px}px; height: ${booking.height_px}px;`"
                                             :class="booking.status === 'disetujui' 
                                                ? 'bg-gradient-to-r from-red-600 via-rose-600 to-marun-900 text-white border border-red-500/40 shadow-red-900/10' 
                                                : 'bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white border border-amber-400/40 shadow-amber-900/10'"
                                             :title="'Klik untuk rincian peminjaman:\n' + booking.peminjam + ' (' + booking.label_waktu + ')\nKeperluan: ' + booking.tujuan">
                                            
                                            <!-- Konten Batang (Jam & Peminjam) -->
                                            <div class="flex items-center gap-2 min-w-0 pr-1">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-black bg-black/25 px-2 py-0.5 rounded-md whitespace-nowrap shrink-0 shadow-2xs">
                                                    <i data-lucide="clock" class="w-3 h-3 text-mustard-300"></i>
                                                    <span x-text="booking.label_waktu"></span>
                                                </span>
                                                <div class="min-w-0 truncate text-xs font-bold leading-tight">
                                                    <span x-text="booking.peminjam"></span>
                                                    <span class="text-white/80 font-normal text-[11px] hidden md:inline" x-text="booking.tujuan ? ' — ' + booking.tujuan : ''"></span>
                                                </div>
                                            </div>

                                            <!-- Badge Status -->
                                            <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-white/20 whitespace-nowrap shrink-0"
                                                  x-text="booking.status === 'disetujui' ? 'Dipinjam' : 'Menunggu'"></span>
                                        </div>
                                    </template>

                                    <!-- Current Time Line pada Row (Presisi tinggi selaras dengan Header Pin) -->
                                    <template x-if="isToday && isOperatingHours">
                                        <div class="absolute top-0 bottom-0 z-20 pointer-events-none -translate-x-1/2"
                                             :style="`left: ${nowPercent}%;`">
                                            <div class="w-0.5 h-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.85)]"></div>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State jika tidak ada data -->
            <div x-show="!loading && filteredMatriks.length === 0" class="p-12 text-center text-gray-500">
                <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="font-bold text-gray-700">Tidak ada fasilitas ditemukan.</p>
                <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter kategori di atas.</p>
            </div>
        </div>
    </div>
    <!-- Akhir Seksi Jadwal & Matriks -->
</div>

<!-- Modal Detail Event Peminjaman -->
<div x-data="{ show: false, event: {} }" 
     x-on:show-event-modal.window="event = $event.detail; show = true"
     class="relative z-50">
    
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" style="display: none;"></div>
    
    <div x-show="show" x-transition style="display: none;" class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div @click.away="show = false" class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-extrabold text-gray-900" x-text="event.title"></h3>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-500 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="mt-2 space-y-3.5 text-sm">
                    <div class="flex items-start gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <i data-lucide="user" class="w-4 h-4 text-marun-700 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Peminjam</p>
                            <p class="font-bold text-gray-900" x-text="event.peminjam"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <i data-lucide="clock" class="w-4 h-4 text-marun-700 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Waktu & Jam Peminjaman</p>
                            <p class="font-bold text-gray-900">
                                <span x-text="new Date(event.start).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' })"></span>
                                <span class="text-gray-400 font-normal">s/d</span>
                                <span x-text="new Date(event.end).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' })"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <i data-lucide="info" class="w-4 h-4 text-marun-700 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Status Verifikasi</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold capitalize border mt-1"
                                  :class="event.status === 'disetujui' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-amber-100 text-amber-800 border-amber-200'" 
                                  x-text="event.status ? event.status.replace('_', ' ') : '-'"></span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <i data-lucide="file-text" class="w-4 h-4 text-marun-700 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Kegiatan / Tujuan</p>
                            <p class="font-medium text-gray-900" x-text="event.tujuan || '-'"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="button" @click="show = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-marun-800 text-mustard-400 font-bold px-4 py-2.5 text-sm hover:bg-marun-700 shadow-sm transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function jadwalSection() {
        return {
            jenis: 'ruangan',
            viewMode: 'matriks',
            subModeAlat: 'kartu',
            filterStatus: 'semua',
            searchKeyword: '',
            tanggal: '{{ date('Y-m-d') }}',
            kategori: 'semua',
            loading: false,
            now: new Date(),
            clockInterval: null,
            rulerHours: ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'],
            isCurrentHourMark(jam) {
                if (!this.isToday) return false;
                const curH = String(this.now.getHours()).padStart(2, '0');
                return jam.startsWith(curH + ':');
            },
            data: {
                tanggal: '{{ date('Y-m-d') }}',
                tanggal_formatted: '',
                jenis: 'ruangan',
                kategori_list: [],
                slot_jam: [],
                matriks: [],
                total_fasilitas: 0,
                terpakai_hari_ini: 0,
                kosong_hari_ini: 0,
                rusak_hari_ini: 0
            },
            get isToday() {
                const d = this.now;
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return this.tanggal === `${year}-${month}-${day}`;
            },
            get isOperatingHours() {
                const now = this.now;
                const curMinutes = now.getHours() * 60 + now.getMinutes();
                return curMinutes >= 7 * 60 && curMinutes <= 21 * 60;
            },
            get nowPercent() {
                const now = this.now;
                // Sertakan detik agar pergerakan indikator benar-benar real-time dan mulus
                const curMinutes = now.getHours() * 60 + now.getMinutes() + (now.getSeconds() / 60);
                const startMinutes = 7 * 60; // 07:00 (420 menit)
                const totalMinutes = 14 * 60; // 840 menit operasional (07:00 - 21:00)
                const pct = ((curMinutes - startMinutes) / totalMinutes) * 100;
                return Math.max(0, Math.min(100, pct));
            },
            get nowTimeFormatted() {
                const now = this.now;
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                return `${h}:${m}`;
            },
            isCurrentSlot(slot) {
                if (!this.isToday || !slot || !slot.mulai || !slot.selesai) return false;
                const now = this.now;
                const curMin = now.getHours() * 60 + now.getMinutes();
                const [startH, startM] = slot.mulai.split(':').map(Number);
                const [endH, endM] = slot.selesai.split(':').map(Number);
                const slotStart = startH * 60 + (startM || 0);
                const slotEnd = endH * 60 + (endM || 0);
                return curMin >= slotStart && curMin < slotEnd;
            },
            scrollToCurrentTime() {
                this.$nextTick(() => {
                    const container = this.$refs.timelineScroll;
                    if (!container) return;
                    // Lebar kolom fasilitas responsif: 145px di layar HP, 260px di desktop
                    const stickyColWidth = window.innerWidth < 640 ? 145 : 260;
                    const timelineWidth = 1260;
                    const pixelLeft = stickyColWidth + (this.nowPercent / 100) * timelineWidth;
                    const scrollLeft = pixelLeft - (container.clientWidth / 2);
                    container.scrollTo({
                        left: Math.max(0, scrollLeft),
                        behavior: 'smooth'
                    });
                });
            },
            get filteredMatriks() {
                if (!this.data.matriks) return [];
                return this.data.matriks.filter(item => {
                    if (this.jenis === 'peralatan') {
                        if (this.filterStatus === 'tersedia') {
                            if (!item.tersedia_hari_ini) return false;
                        }
                        if (this.filterStatus === 'terpakai') {
                            if (!item.has_booking) return false;
                        }
                        if (this.filterStatus === 'perbaikan') {
                            const isRusak = item.status_fisik === 'perbaikan' || item.kondisi === 'rusak_ringan' || item.kondisi === 'rusak_berat';
                            if (!isRusak) return false;
                        }
                    }
                    if (this.searchKeyword && this.searchKeyword.trim() !== '') {
                        const q = this.searchKeyword.toLowerCase().trim();
                        const nama = (item.nama_fasilitas || '').toLowerCase();
                        const kode = (item.kode_fasilitas || '').toLowerCase();
                        const kat = (item.kategori || '').toLowerCase();
                        if (!nama.includes(q) && !kode.includes(q) && !kat.includes(q)) {
                            return false;
                        }
                    }
                    return true;
                });
            },
            init() {
                this.now = new Date();
                // Jalankan interval per 1 detik agar clock dan indikator bergerak live
                if (this.clockInterval) clearInterval(this.clockInterval);
                this.clockInterval = setInterval(() => {
                    this.now = new Date();
                }, 1000);

                this.fetchMatriks();

                // Otomatis pusatkan layar ke waktu saat ini
                setTimeout(() => {
                    if (this.isToday && this.isOperatingHours) {
                        this.scrollToCurrentTime();
                    }
                }, 600);
            },
            fetchMatriks() {
                this.loading = true;
                const url = `/api/fasilitas/matriks-jadwal?tanggal=${encodeURIComponent(this.tanggal)}&kategori=${encodeURIComponent(this.kategori)}&jenis=${encodeURIComponent(this.jenis)}`;
                fetch(url)
                    .then(res => res.json())
                    .then(resData => {
                        this.data = resData;
                        this.loading = false;
                        this.$nextTick(() => {
                            if (window.lucide) {
                                lucide.createIcons();
                            }
                        });
                    })
                    .catch(err => {
                        console.error('Error fetching matriks:', err);
                        this.loading = false;
                    });
            },
            changeDate(offsetDays) {
                const parts = this.tanggal.split('-');
                const d = new Date(parts[0], parts[1] - 1, parts[2]);
                d.setDate(d.getDate() + offsetDays);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                this.tanggal = `${year}-${month}-${day}`;
                this.fetchMatriks();
            },
            today() {
                const d = new Date();
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                this.tanggal = `${year}-${month}-${day}`;
                this.fetchMatriks();
            },
            setKategori(kat) {
                this.kategori = kat;
                this.fetchMatriks();
            },
            setJenis(j) {
                this.jenis = j;
                this.kategori = 'semua';
                this.filterStatus = 'semua';
                this.searchKeyword = '';
                this.fetchMatriks();
                if (j === 'ruangan' || this.subModeAlat === 'timeline') {
                    setTimeout(() => {
                        if (this.isToday && this.isOperatingHours) {
                            this.scrollToCurrentTime();
                        }
                    }, 400);
                }
            },
            setFilterStatus(st) {
                this.filterStatus = st;
            },
            openDetail(slot) {
                window.dispatchEvent(new CustomEvent('show-event-modal', {
                    detail: {
                        title: slot.ruangan,
                        start: slot.start_str,
                        end: slot.end_str,
                        peminjam: slot.peminjam,
                        status: slot.status,
                        tujuan: slot.tujuan
                    }
                }));
                setTimeout(() => {
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                }, 50);
            },
            setSubModeAlat(mode) {
                this.subModeAlat = mode;
                if (mode === 'timeline') {
                    setTimeout(() => {
                        if (this.isToday && this.isOperatingHours) {
                            this.scrollToCurrentTime();
                        }
                    }, 200);
                }
            }
        };
    }
</script>
@endpush
