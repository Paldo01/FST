@extends('layouts.app')

@section('title', 'Pengembalian Alat - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="pengembalianHandler()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Pengembalian Alat
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Kelola konfirmasi pengembalian alat yang dipinjam
        </p>
    </div>

    <!-- Container Utama (Sesuai Mockup: Container Putih Membungkus Header, Filter & Daftar Pengembalian) -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-7 space-y-6">
        
        <!-- Header Container: Judul Section & Filter Bar Sejajar (Sesuai Mockup) -->
        <div class="flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-4 pb-1">
            
            <!-- Judul & Badge Counter -->
            <div class="flex items-center gap-2.5 shrink-0">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                    Daftar Pengembalian
                </h2>
                <span class="bg-gray-100 text-gray-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-gray-200/60">
                    {{ $daftarPengembalian->total() }} Pengembalian
                </span>
            </div>

            <!-- Filter Bar (Pencarian, Tanggal, & Tombol Cari Sesuai Mockup) -->
            <form method="GET" action="{{ route('admin.pengembalian.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 max-w-3xl">
                
                <!-- Pencarian Nama atau NIM Peminjam -->
                <div class="relative flex-1 min-w-[240px]">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                    <input type="text" name="cari" value="{{ request('cari') }}" 
                           placeholder="Cari nama atau NIM peminjam" 
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
                </div>

                <!-- Input Tanggal (Menampilkan 'Pilih Tanggal' Sesuai Mockup) -->
                <div class="relative sm:w-48 shrink-0">
                    <input type="{{ request('tanggal') ? 'date' : 'text' }}" 
                           name="tanggal" 
                           value="{{ request('tanggal') }}" 
                           placeholder="Pilih Tanggal"
                           onfocus="(this.type='date')"
                           onblur="if(!this.value){this.type='text'}"
                           class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer">
                    <i data-lucide="calendar" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                </div>

                <!-- Tombol Cari (Solid Maroon Sesuai Mockup) -->
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl sm:rounded-2xl bg-[#801414] hover:bg-[#681010] active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer select-none">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari</span>
                </button>

                <!-- Tombol Reset Filter -->
                @if(request()->hasAny(['cari', 'tanggal']))
                    <a href="{{ route('admin.pengembalian.index') }}" 
                       class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold transition-colors flex items-center justify-center gap-1 shrink-0" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        <span class="sm:hidden">Reset</span>
                    </a>
                @endif
            </form>

        </div>

        <!-- Daftar Card Pengembalian (Sesuai Mockup) -->
        <div class="space-y-4">
            @forelse($daftarPengembalian as $p)
                @php
                    $isSudahDikembalikan = ($p->pengembalian !== null) || ($p->status_pengajuan === 'selesai');
                @endphp

                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 p-5 sm:p-6 transition-all hover:border-gray-300 shadow-2xs">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 lg:gap-6">
                        
                        <!-- Kolom 1: Profil & Identitas Pemohon (Sesuai Mockup) -->
                        <div class="flex items-center gap-3.5 sm:gap-4 w-full lg:w-[32%] xl:w-[30%] shrink-0">
                            <!-- Avatar Soft Pink Sesuai Mockup -->
                            <div class="w-13 h-13 sm:w-15 sm:h-15 rounded-full bg-[#FDE8E8] flex items-center justify-center shrink-0 border border-red-100 shadow-2xs">
                                <i data-lucide="user" class="w-6 h-6 sm:w-7 sm:h-7 text-[#DC2626]"></i>
                            </div>
                            
                            <!-- Nama & Info Pemohon (Tanpa Truncate Agak Tidak Terpotong) -->
                            <div class="min-w-0">
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-snug">
                                    {{ $p->pengguna->nama_lengkap }} 
                                    <span class="text-gray-600 font-semibold text-xs sm:text-sm">
                                        ({{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }})
                                    </span>
                                </h3>
                                <p class="text-xs text-gray-500 font-medium mt-0.5 whitespace-nowrap">
                                    {{ ($p->pengguna->jenis_pengguna === 'dosen') ? 'Nip' : 'Nim' }} {{ $p->pengguna->nomor_identitas ?? '-' }} 
                                    <span class="text-gray-300 mx-1">|</span> 
                                    <span>{{ ($p->pengguna->jenis_pengguna === 'dosen') ? 'FST' : 'Sistem Informasi' }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Divider Kolom 1 ke 2 -->
                        <div class="hidden lg:block w-px bg-gray-200/80 self-stretch my-1"></div>

                        <!-- Kolom 2: Detail Peminjaman (Peminjaman, Tanggal, Waktu Sesuai Mockup) -->
                        <div class="w-full lg:flex-1 space-y-1.5 text-xs sm:text-sm">
                            <!-- Peminjaman -->
                            <div class="flex items-center gap-2">
                                <i data-lucide="package" class="w-4 h-4 text-gray-500 shrink-0"></i>
                                <span class="text-gray-500 font-medium w-24 shrink-0">Peminjaman</span>
                                <span class="text-gray-400 font-medium">:</span>
                                <span class="font-bold text-gray-900">{{ $p->fasilitas->nama_fasilitas }}</span>
                            </div>
                            
                            <!-- Tanggal -->
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-gray-500 shrink-0"></i>
                                <span class="text-gray-500 font-medium w-24 shrink-0">Tanggal</span>
                                <span class="text-gray-400 font-medium">:</span>
                                <span class="font-medium text-gray-800">{{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d F Y') : '-' }}</span>
                            </div>

                            <!-- Waktu -->
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-500 shrink-0"></i>
                                <span class="text-gray-500 font-medium w-24 shrink-0">Waktu</span>
                                <span class="text-gray-400 font-medium">:</span>
                                <span class="font-medium text-gray-800">
                                    {{ $p->tanggal_mulai ? $p->tanggal_mulai->format('H.i') : '' }} - {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('H.i') : '' }}
                                </span>
                            </div>
                        </div>

                        <!-- Divider Kolom 2 ke 3 -->
                        <div class="hidden lg:block w-px bg-gray-200/80 self-stretch my-1"></div>

                        <!-- Kolom 3: Status & Tombol Aksi (Sesuai Mockup) -->
                        <div class="w-full lg:w-[260px] xl:w-[280px] shrink-0 flex flex-col items-center justify-center gap-2.5 pt-3 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                            
                            @if(!$isSudahDikembalikan)
                                <!-- Badge: Belum dikembalikan (Soft Yellow Sesuai Mockup) -->
                                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-[#FEF9C3] text-[#A16207] border border-[#FDE047]/60 whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-[#CA8A04]"></span>
                                    <span>Belum dikembalikan</span>
                                </span>

                                <!-- Tombol: Konfirmasi Pengembalian (Satu Baris Sesuai Mockup) -->
                                <button type="button" 
                                        @click="bukaModalKonfirmasi({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}', '{{ $p->fasilitas->kondisi_awal }}')"
                                        class="w-full max-w-[240px] px-4 py-2 bg-white hover:bg-red-50 text-[#801414] border border-[#801414] rounded-xl sm:rounded-2xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap">
                                    <i data-lucide="package" class="w-4 h-4 text-[#801414] shrink-0"></i>
                                    <span>Konfirmasi Pengembalian</span>
                                </button>
                            @else
                                <!-- Badge: Sudah dikembalikan (Soft Green Sesuai Mockup) -->
                                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-[#DCFCE7] text-[#15803D] border border-[#86EFAC]/60 whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span>
                                    <span>Sudah dikembalikan</span>
                                </span>

                                <!-- Tombol: Detail -> (Sesuai Mockup) -->
                                <button type="button" 
                                        @click="bukaModalDetail({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }}', '{{ $p->pengguna->nomor_identitas ?? '-' }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d F Y') : '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->format('H.i') : '' }} - {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('H.i') : '' }}', '{{ $p->pengembalian ? $p->pengembalian->tanggal_pengembalian->translatedFormat('d F Y, H.i') : '-' }}', '{{ $p->pengembalian ? ucfirst($p->pengembalian->kondisi_akhir) : 'Baik' }}', '{{ addslashes($p->pengembalian->catatan_admin ?? '-') }}', '{{ addslashes($p->pengembalian->admin->nama_lengkap ?? 'Admin FST') }}')"
                                        class="px-5 py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 transition-all shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap">
                                    <span>Detail</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 shrink-0"></i>
                                </button>
                            @endif

                        </div>

                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="py-12 text-center border border-dashed border-gray-200 rounded-2xl">
                    <div class="w-16 h-16 bg-red-50 text-[#801414] rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-2xs">
                        <i data-lucide="package-open" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">
                        Tidak Ada Data Pengembalian
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                        @if(request()->hasAny(['cari', 'tanggal']))
                            Tidak ditemukan data pengembalian yang sesuai dengan filter pencarian.
                        @else
                            Saat ini belum ada peminjaman yang disetujui atau menunggu pengembalian.
                        @endif
                    </p>
                    @if(request()->hasAny(['cari', 'tanggal']))
                        <a href="{{ route('admin.pengembalian.index') }}" 
                           class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs sm:text-sm font-bold transition-colors">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            <span>Tampilkan Semua</span>
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($daftarPengembalian->hasPages())
            <div class="pt-4 border-t border-gray-100">
                {{ $daftarPengembalian->links() }}
            </div>
        @endif

    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: Form Konfirmasi Pengembalian      -->
    <!-- ========================================== -->
    <div x-show="modalKonfirmasiOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="modalKonfirmasiOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-gray-100 z-10 space-y-5">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#801414] border border-red-100 flex items-center justify-center shrink-0">
                            <i data-lucide="package-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">
                                Konfirmasi Pengembalian
                            </h3>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                Catat penerimaan dan kondisi fasilitas yang dikembalikan
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="modalKonfirmasiOpen = false" class="text-gray-400 hover:text-gray-700 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Info Peminjam & Fasilitas Ringkas -->
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 grid grid-cols-2 gap-3 text-xs sm:text-sm">
                    <div>
                        <span class="text-gray-500 block text-[11px]">Nama Peminjam</span>
                        <span class="font-bold text-gray-900" x-text="targetPemohon"></span>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[11px]">Fasilitas</span>
                        <span class="font-bold text-gray-900" x-text="targetFasilitas"></span>
                    </div>
                </div>

                <!-- Form Konfirmasi -->
                <form :action="'{{ url('admin/pengembalian') }}/' + targetId" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Pilihan Kondisi Akhir -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Kondisi Akhir Saat Dikembalikan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer border-2 border-gray-200 rounded-2xl p-3 flex flex-col items-center text-center hover:bg-gray-50 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                                <input type="radio" name="kondisi_akhir" value="baik" class="sr-only" required checked>
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-1.5">
                                    <i data-lucide="thumbs-up" class="w-5 h-5"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-900">Baik</span>
                                <span class="text-[10px] text-gray-500">Siap dipinjam</span>
                            </label>
                            
                            <label class="cursor-pointer border-2 border-gray-200 rounded-2xl p-3 flex flex-col items-center text-center hover:bg-gray-50 transition-all has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50/50">
                                <input type="radio" name="kondisi_akhir" value="rusak" class="sr-only">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-1.5">
                                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-900">Rusak</span>
                                <span class="text-[10px] text-gray-500">Perlu perbaikan</span>
                            </label>
                            
                            <label class="cursor-pointer border-2 border-gray-200 rounded-2xl p-3 flex flex-col items-center text-center hover:bg-gray-50 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50/50">
                                <input type="radio" name="kondisi_akhir" value="hilang" class="sr-only">
                                <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center mb-1.5">
                                    <i data-lucide="help-circle" class="w-5 h-5"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-900">Hilang</span>
                                <span class="text-[10px] text-gray-500">Tindak lanjuti</span>
                            </label>
                        </div>
                    </div>

                    <!-- Catatan Admin (Opsional) -->
                    <div>
                        <label for="catatan_admin" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Catatan Pemeriksaan (Opsional)
                        </label>
                        <textarea name="catatan_admin" id="catatan_admin" rows="3" 
                            class="w-full p-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all"
                            placeholder="Catatan terkait kelengkapan, kabel, kebersihan, atau kerusakan..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" @click="modalKonfirmasiOpen = false" 
                                class="flex-1 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl sm:rounded-2xl text-xs sm:text-sm transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 py-2.5 sm:py-3 bg-[#801414] hover:bg-[#681010] active:scale-[0.99] text-white font-bold rounded-xl sm:rounded-2xl text-xs sm:text-sm shadow-xs transition-all cursor-pointer">
                            Simpan Pengembalian
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: Detail Pengembalian Selesai       -->
    <!-- ========================================== -->
    <div x-show="modalDetailOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="modalDetailOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-gray-100 z-10 space-y-5">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">
                                Detail Pengembalian
                            </h3>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                Fasilitas telah selesai dikembalikan
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="modalDetailOpen = false" class="text-gray-400 hover:text-gray-700 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Konten Detail -->
                <div class="space-y-4 text-xs sm:text-sm">
                    
                    <!-- Info Pemohon & Fasilitas -->
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-gray-500 block text-[11px]">Nama Peminjam</span>
                            <span class="font-bold text-gray-900" x-text="detailData.namaPemohon"></span>
                            <span class="text-[11px] text-gray-500 block" x-text="detailData.jenisPemohon + ' • ' + detailData.nomorIdentitas"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[11px]">Fasilitas</span>
                            <span class="font-bold text-gray-900" x-text="detailData.namaFasilitas"></span>
                        </div>
                    </div>

                    <!-- Jadwal Pemakaian & Waktu Dikembalikan -->
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-gray-500 block text-[11px]">Jadwal Peminjaman</span>
                            <span class="font-semibold text-gray-800" x-text="detailData.tanggal"></span>
                            <span class="text-[11px] text-gray-500 block" x-text="detailData.waktu"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[11px]">Waktu Pengembalian</span>
                            <span class="font-semibold text-gray-800" x-text="detailData.tanggalPengembalian"></span>
                        </div>
                    </div>

                    <!-- Kondisi & Catatan -->
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 text-[11px]">Kondisi Akhir</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                                  :class="{
                                      'bg-emerald-100 text-emerald-800': detailData.kondisiAkhir.toLowerCase() === 'baik',
                                      'bg-orange-100 text-orange-800': detailData.kondisiAkhir.toLowerCase() === 'rusak',
                                      'bg-red-100 text-red-800': detailData.kondisiAkhir.toLowerCase() === 'hilang'
                                  }" x-text="detailData.kondisiAkhir">
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[11px]">Catatan Admin</span>
                            <p class="font-medium text-gray-900 mt-0.5 leading-relaxed" x-text="detailData.catatanAdmin"></p>
                        </div>
                        <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between text-[11px] text-gray-500">
                            <span>Diverifikasi oleh</span>
                            <span class="font-bold text-gray-700" x-text="detailData.adminPetugas"></span>
                        </div>
                    </div>

                </div>

                <!-- Footer Modal -->
                <div class="pt-2 flex justify-end">
                    <button type="button" @click="modalDetailOpen = false" 
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl sm:rounded-2xl text-xs sm:text-sm transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Alpine.js Handler -->
<script>
    function pengembalianHandler() {
        return {
            modalKonfirmasiOpen: false,
            modalDetailOpen: false,
            targetId: null,
            targetPemohon: '',
            targetFasilitas: '',
            targetKondisiAwal: '',
            detailData: {
                id: null,
                namaPemohon: '',
                jenisPemohon: '',
                nomorIdentitas: '',
                namaFasilitas: '',
                tanggal: '',
                waktu: '',
                tanggalPengembalian: '',
                kondisiAkhir: '',
                catatanAdmin: '',
                adminPetugas: ''
            },

            bukaModalKonfirmasi(id, namaPemohon, namaFasilitas, kondisiAwal) {
                this.targetId = id;
                this.targetPemohon = namaPemohon;
                this.targetFasilitas = namaFasilitas;
                this.targetKondisiAwal = kondisiAwal;
                this.modalKonfirmasiOpen = true;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            },

            bukaModalDetail(id, namaPemohon, jenisPemohon, nomorIdentitas, namaFasilitas, tanggal, waktu, tanggalPengembalian, kondisiAkhir, catatanAdmin, adminPetugas) {
                this.detailData = {
                    id: id,
                    namaPemohon: namaPemohon,
                    jenisPemohon: jenisPemohon,
                    nomorIdentitas: nomorIdentitas,
                    namaFasilitas: namaFasilitas,
                    tanggal: tanggal,
                    waktu: waktu,
                    tanggalPengembalian: tanggalPengembalian,
                    kondisiAkhir: kondisiAkhir,
                    catatanAdmin: catatanAdmin,
                    adminPetugas: adminPetugas
                };
                this.modalDetailOpen = true;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            }
        };
    }
</script>
@endsection
