@extends('layouts.app')

@section('title', 'Pengajuan Peminjaman - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12" x-data="pengajuanHandler()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Pengajuan Peminjaman
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Kelola dan verifikasi pengajuan peminjaman ruang dan alat FST
        </p>
    </div>

    <!-- Filter Bar (Sesuai Mockup) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.verifikasi.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            
            <!-- Pencarian Nama Peminjaman atau Fasilitas -->
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input type="text" name="cari" value="{{ request('cari') }}" 
                       placeholder="Cari nama peminjaman atau fasilitas" 
                       class="w-full pl-11 pr-4 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
            </div>

            <!-- Dropdown Semua Status -->
            <div class="relative md:w-52 shrink-0">
                <select name="status" 
                        class="w-full appearance-none px-4 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer pr-10">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
                <i data-lucide="chevron-down" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </div>

            <!-- Filter Tanggal (Pilih Tanggal) -->
            <div class="relative md:w-48 shrink-0">
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                       placeholder="Pilih Tanggal"
                       class="w-full px-4 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer">
            </div>

            <!-- Tombol Cari (Solid Maroon Sesuai Mockup) -->
            <button type="submit" 
                    class="px-6 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl bg-[#801414] hover:bg-[#681010] active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer select-none">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Cari</span>
            </button>

            <!-- Reset Filter Jika Sedang Ada Filter -->
            @if(request()->hasAny(['cari', 'status', 'tanggal']))
                <a href="{{ route('admin.verifikasi.index') }}" 
                   class="px-3.5 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold transition-colors flex items-center justify-center gap-1.5 shrink-0" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span class="md:hidden lg:inline">Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Section Daftar Pengajuan (Sesuai Mockup) -->
    <div class="space-y-4">
        
        <!-- Header Section -->
        <div class="flex items-center gap-2.5">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                Daftar Pengajuan
            </h2>
            <span class="bg-gray-200/70 text-gray-600 text-xs px-2.5 py-0.5 rounded-full font-bold">
                {{ $pengajuan->total() }} Pengajuan
            </span>
        </div>

        <!-- Daftar Card Pengajuan (Sesuai Mockup) -->
        <div class="space-y-4">
            @forelse($pengajuan as $p)
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-2xs p-5 sm:p-6 transition-all hover:border-gray-200">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 lg:gap-6">
                        
                        <!-- Kolom 1: Profil & Identitas Pemohon -->
                        <div class="flex items-center gap-4 w-full lg:w-[28%] shrink-0">
                            <!-- Avatar Soft Pink Sesuai Mockup -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#FDE8E8] flex items-center justify-center shrink-0 border border-red-100 shadow-2xs">
                                <i data-lucide="user" class="w-7 h-7 sm:w-8 sm:h-8 text-[#DC2626]"></i>
                            </div>
                            
                            <!-- Nama & Info Pemohon -->
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate">
                                    {{ $p->pengguna->nama_lengkap }} 
                                    <span class="text-gray-600 font-semibold text-xs sm:text-sm">
                                        ({{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }})
                                    </span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-0.5 truncate">
                                    {{ ($p->pengguna->jenis_pengguna === 'dosen') ? 'Nip' : 'Nim' }} {{ $p->pengguna->nomor_identitas ?? '-' }} 
                                    <span class="text-gray-300 mx-1">|</span> 
                                    <span>{{ ($p->pengguna->jenis_pengguna === 'dosen') ? 'FST' : 'Sistem Informasi' }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Divider Kolom 1 ke 2 -->
                        <div class="hidden lg:block w-px bg-gray-200/80 self-stretch my-1"></div>

                        <!-- Kolom 2: Detail Peminjaman (Icon List Sesuai Mockup) -->
                        <div class="w-full lg:flex-1 space-y-2 text-xs sm:text-sm">
                            <!-- Peminjaman -->
                            <div class="flex items-center gap-2">
                                <i data-lucide="package" class="w-4 h-4 text-gray-500 shrink-0"></i>
                                <span class="text-gray-500 font-medium w-24 shrink-0">Peminjaman</span>
                                <span class="text-gray-400 font-medium">:</span>
                                <span class="font-bold text-gray-900 truncate">{{ $p->fasilitas->nama_fasilitas }}</span>
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

                            <!-- Keperluan -->
                            <div class="flex items-center gap-2">
                                <i data-lucide="file-text" class="w-4 h-4 text-gray-500 shrink-0"></i>
                                <span class="text-gray-500 font-medium w-24 shrink-0">Keperluan</span>
                                <span class="text-gray-400 font-medium">:</span>
                                <span class="font-medium text-gray-800 truncate">{{ $p->tujuan_peminjaman }}</span>
                            </div>
                        </div>

                        <!-- Divider Kolom 2 ke 3 -->
                        <div class="hidden lg:block w-px bg-gray-200/80 self-stretch my-1"></div>

                        <!-- Kolom 3: Status & Tombol Aksi (Sesuai Mockup) -->
                        <div class="w-full lg:w-[24%] shrink-0 flex flex-col items-center justify-center gap-3 pt-3 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                            
                            <!-- Status Badges -->
                            @if($p->status_pengajuan === 'menunggu')
                                <!-- Badge Pending (Soft Yellow Sesuai Mockup) -->
                                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-[#FEF9C3] text-[#A16207] border border-[#FDE047]/60">
                                    <span class="w-2 h-2 rounded-full bg-[#CA8A04]"></span>
                                    <span>Pending</span>
                                </span>

                                <!-- Tombol Setujui & Tolak Sesuai Mockup -->
                                <div class="flex items-center gap-2.5 w-full justify-center">
                                    <!-- Setujui (Green Outline Sesuai Mockup) -->
                                    <button type="button" 
                                            @click="bukaModalSetuju({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}')"
                                            class="flex-1 max-w-[110px] px-3.5 py-1.5 sm:py-2 bg-white hover:bg-emerald-50 text-emerald-600 border border-emerald-500 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition-all shadow-2xs active:scale-95 cursor-pointer">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                        <span>Setujui</span>
                                    </button>

                                    <!-- Tolak (Red Outline Sesuai Mockup) -->
                                    <button type="button" 
                                            @click="bukaModalTolak({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}')"
                                            class="flex-1 max-w-[110px] px-3.5 py-1.5 sm:py-2 bg-white hover:bg-red-50 text-red-500 border border-red-400 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition-all shadow-2xs active:scale-95 cursor-pointer">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                        <span>Tolak</span>
                                    </button>
                                </div>

                            @elseif($p->status_pengajuan === 'disetujui')
                                <!-- Badge Disetujui (Soft Green Sesuai Mockup) -->
                                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-[#DCFCE7] text-[#15803D] border border-[#86EFAC]/60">
                                    <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span>
                                    <span>Disetujui</span>
                                </span>

                                <!-- Tombol Lihat -> Sesuai Mockup -->
                                <button type="button" 
                                        @click="bukaModalDetail({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }}', '{{ $p->pengguna->nomor_identitas ?? '-' }}', '{{ $p->pengguna->nomor_telepon ?? '-' }}', '{{ $p->pengguna->email }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}', '{{ $p->fasilitas->kode_fasilitas }}', '{{ $p->fasilitas->kategori->nama_kategori ?? '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d F Y') : '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->format('H.i') : '' }} - {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('H.i') : '' }}', '{{ addslashes($p->tujuan_peminjaman) }}', '{{ $p->status_pengajuan }}', '{{ addslashes($p->alasan_penolakan ?? '') }}', '{{ $p->berkas_persyaratan ? Storage::url($p->berkas_persyaratan) : '' }}', '{{ $p->created_at ? $p->created_at->translatedFormat('d F Y, H.i') : '-' }}')"
                                        class="px-4 py-1.5 sm:py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200/90 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 transition-all shadow-2xs active:scale-95 cursor-pointer">
                                    <span>Lihat</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </button>

                            @elseif($p->status_pengajuan === 'ditolak')
                                <!-- Badge Ditolak (Soft Red Sesuai Mockup) -->
                                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-[#FEE2E2] text-[#B91C1C] border border-[#FCA5A5]/60">
                                    <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                                    <span>Ditolak</span>
                                </span>

                                <!-- Tombol Lihat -> Sesuai Mockup -->
                                <button type="button" 
                                        @click="bukaModalDetail({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }}', '{{ $p->pengguna->nomor_identitas ?? '-' }}', '{{ $p->pengguna->nomor_telepon ?? '-' }}', '{{ $p->pengguna->email }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}', '{{ $p->fasilitas->kode_fasilitas }}', '{{ $p->fasilitas->kategori->nama_kategori ?? '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d F Y') : '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->format('H.i') : '' }} - {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('H.i') : '' }}', '{{ addslashes($p->tujuan_peminjaman) }}', '{{ $p->status_pengajuan }}', '{{ addslashes($p->alasan_penolakan ?? '') }}', '{{ $p->berkas_persyaratan ? Storage::url($p->berkas_persyaratan) : '' }}', '{{ $p->created_at ? $p->created_at->translatedFormat('d F Y, H.i') : '-' }}')"
                                        class="px-4 py-1.5 sm:py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200/90 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 transition-all shadow-2xs active:scale-95 cursor-pointer">
                                    <span>Lihat</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </button>

                            @else
                                <!-- Status Lainnya (e.g. Selesai) -->
                                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-[#EFF6FF] text-[#1D4ED8] border border-[#93C5FD]/60">
                                    <span class="w-2 h-2 rounded-full bg-[#2563EB]"></span>
                                    <span>{{ ucfirst($p->status_pengajuan) }}</span>
                                </span>

                                <button type="button" 
                                        @click="bukaModalDetail({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }}', '{{ $p->pengguna->nomor_identitas ?? '-' }}', '{{ $p->pengguna->nomor_telepon ?? '-' }}', '{{ $p->pengguna->email }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}', '{{ $p->fasilitas->kode_fasilitas }}', '{{ $p->fasilitas->kategori->nama_kategori ?? '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d F Y') : '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->format('H.i') : '' }} - {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('H.i') : '' }}', '{{ addslashes($p->tujuan_peminjaman) }}', '{{ $p->status_pengajuan }}', '{{ addslashes($p->alasan_penolakan ?? '') }}', '{{ $p->berkas_persyaratan ? Storage::url($p->berkas_persyaratan) : '' }}', '{{ $p->created_at ? $p->created_at->translatedFormat('d F Y, H.i') : '-' }}')"
                                        class="px-4 py-1.5 sm:py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200/90 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 transition-all shadow-2xs active:scale-95 cursor-pointer">
                                    <span>Lihat</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </button>
                            @endif

                        </div>

                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white rounded-3xl border border-dashed border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-red-50 text-[#801414] rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-2xs">
                        <i data-lucide="file-question" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">
                        Tidak Ada Pengajuan Ditemukan
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                        @if(request()->hasAny(['cari', 'status', 'tanggal']))
                            Tidak ada pengajuan yang sesuai dengan filter pencarian Anda. Silakan coba atur ulang filter.
                        @else
                            Saat ini belum ada pengajuan peminjaman ruang atau alat dari civitas FST.
                        @endif
                    </p>
                    @if(request()->hasAny(['cari', 'status', 'tanggal']))
                        <a href="{{ route('admin.verifikasi.index') }}" 
                           class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs sm:text-sm font-bold transition-colors">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            <span>Tampilkan Semua Pengajuan</span>
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($pengajuan->hasPages())
            <div class="mt-6">
                {{ $pengajuan->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: Konfirmasi Setujui Pengajuan       -->
    <!-- ========================================== -->
    <div x-show="modalSetujuOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="modalSetujuOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-gray-100 z-10 text-center">
                
                <!-- Icon Header Check -->
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle-2" class="w-9 h-9"></i>
                </div>

                <h3 class="text-xl font-black text-gray-900">
                    Setujui Peminjaman
                </h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin menyetujui peminjaman fasilitas <br>
                    <strong class="text-gray-900" x-text="targetFasilitas"></strong> oleh 
                    <strong class="text-gray-900" x-text="targetPemohon"></strong>?
                </p>

                <!-- Form Action -->
                <form :action="'{{ url('admin/verifikasi') }}/' + targetId + '/setujui'" method="POST" class="mt-6 flex items-center gap-3">
                    @csrf
                    <button type="button" @click="modalSetujuOpen = false" 
                            class="flex-1 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl sm:rounded-2xl text-xs sm:text-sm transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 py-2.5 sm:py-3 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold rounded-xl sm:rounded-2xl text-xs sm:text-sm shadow-sm transition-all cursor-pointer">
                        Ya, Setujui
                    </button>
                </form>

            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: Tolak Pengajuan Peminjaman         -->
    <!-- ========================================== -->
    <div x-show="modalTolakOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="modalTolakOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-gray-100 z-10">
                
                <!-- Icon & Title -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-black text-gray-900">
                            Tolak Pengajuan
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Pengajuan oleh: <span class="font-bold text-gray-800" x-text="targetPemohon"></span>
                        </p>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-gray-600 mb-4">
                    Mohon cantumkan alasan penolakan secara jelas agar peminjam dapat mengetahui penyebabnya.
                </p>

                <!-- Form Action Tolak -->
                <form :action="'{{ url('admin/verifikasi') }}/' + targetId + '/tolak'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alasan_penolakan" rows="3" required
                                  placeholder="Contoh: Jadwal bentrok dengan kegiatan fakultas / berkas belum lengkap..."
                                  class="w-full p-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" @click="modalTolakOpen = false" 
                                class="flex-1 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl sm:rounded-2xl text-xs sm:text-sm transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 py-2.5 sm:py-3 bg-red-600 hover:bg-red-700 active:scale-[0.99] text-white font-bold rounded-xl sm:rounded-2xl text-xs sm:text-sm shadow-sm transition-all cursor-pointer">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: Detail Lengkap Pengajuan           -->
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
            <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-gray-100 z-10 space-y-5">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">
                            Detail Pengajuan Peminjaman
                        </h3>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                            Diajukan pada <span x-text="detailData.diajukanPada"></span>
                        </p>
                    </div>
                    <button type="button" @click="modalDetailOpen = false" class="text-gray-400 hover:text-gray-700 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Konten Detail -->
                <div class="space-y-4 text-xs sm:text-sm">
                    
                    <!-- Box 1: Info Pemohon -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Pemohon</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-gray-500 block text-[11px]">Nama Pemohon</span>
                                <span class="font-bold text-gray-900" x-text="detailData.namaPemohon"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Status / Peran</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.jenisPemohon"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">NIM / NIP</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.nomorIdentitas"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">No. Telepon / WA</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.nomorTelepon"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Info Fasilitas & Waktu -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Fasilitas & Jadwal</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-gray-500 block text-[11px]">Fasilitas</span>
                                <span class="font-bold text-gray-900" x-text="detailData.namaFasilitas"></span>
                                <span class="text-[11px] text-gray-500 block" x-text="'Kode: ' + detailData.kodeFasilitas"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Kategori</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.kategori"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Tanggal Pelaksanaan</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.tanggal"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Rentang Waktu</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.waktu"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Box 3: Keperluan / Tujuan -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Keperluan / Agenda</h4>
                        <p class="text-gray-900 font-medium leading-relaxed" x-text="detailData.keperluan"></p>
                    </div>

                    <!-- Box 4: Berkas Persyaratan (Jika Ada) -->
                    <template x-if="detailData.berkasUrl">
                        <div class="bg-blue-50/60 rounded-2xl p-4 border border-blue-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                                    <i data-lucide="file-check" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900">Berkas Lampiran / Proposal</h5>
                                    <p class="text-[11px] text-gray-500">Tersedia dokumen persyaratan peminjaman</p>
                                </div>
                            </div>
                            <a :href="detailData.berkasUrl" target="_blank" 
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors shadow-2xs">
                                <span>Lihat Berkas</span>
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </template>

                    <!-- Box 5: Status & Alasan Penolakan Jika Ada -->
                    <div class="p-3 rounded-2xl border flex items-center justify-between"
                         :class="{
                             'bg-yellow-50/80 border-yellow-200 text-yellow-800': detailData.status === 'menunggu',
                             'bg-green-50/80 border-green-200 text-green-800': detailData.status === 'disetujui',
                             'bg-red-50/80 border-red-200 text-red-800': detailData.status === 'ditolak',
                             'bg-blue-50/80 border-blue-200 text-blue-800': detailData.status === 'selesai'
                         }">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider block">Status Pengajuan</span>
                            <span class="font-black text-sm capitalize" x-text="detailData.status"></span>
                        </div>
                        <template x-if="detailData.status === 'ditolak' && detailData.alasanPenolakan">
                            <div class="text-right max-w-xs">
                                <span class="text-[11px] font-bold text-red-600 block">Alasan Ditolak:</span>
                                <span class="text-xs text-red-800 italic" x-text="detailData.alasanPenolakan"></span>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Footer Modal -->
                <div class="pt-2 flex justify-end">
                    <button type="button" @click="modalDetailOpen = false" 
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs sm:text-sm transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Alpine.js Handler -->
<script>
    function pengajuanHandler() {
        return {
            modalSetujuOpen: false,
            modalTolakOpen: false,
            modalDetailOpen: false,
            targetId: null,
            targetPemohon: '',
            targetFasilitas: '',
            detailData: {
                id: null,
                namaPemohon: '',
                jenisPemohon: '',
                nomorIdentitas: '',
                nomorTelepon: '',
                email: '',
                namaFasilitas: '',
                kodeFasilitas: '',
                kategori: '',
                tanggal: '',
                waktu: '',
                keperluan: '',
                status: '',
                alasanPenolakan: '',
                berkasUrl: '',
                diajukanPada: ''
            },

            bukaModalSetuju(id, namaPemohon, namaFasilitas) {
                this.targetId = id;
                this.targetPemohon = namaPemohon;
                this.targetFasilitas = namaFasilitas;
                this.modalSetujuOpen = true;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            },

            bukaModalTolak(id, namaPemohon, namaFasilitas) {
                this.targetId = id;
                this.targetPemohon = namaPemohon;
                this.targetFasilitas = namaFasilitas;
                this.modalTolakOpen = true;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            },

            bukaModalDetail(id, namaPemohon, jenisPemohon, nomorIdentitas, nomorTelepon, email, namaFasilitas, kodeFasilitas, kategori, tanggal, waktu, keperluan, status, alasanPenolakan, berkasUrl, diajukanPada) {
                this.detailData = {
                    id: id,
                    namaPemohon: namaPemohon,
                    jenisPemohon: jenisPemohon,
                    nomorIdentitas: nomorIdentitas,
                    nomorTelepon: nomorTelepon,
                    email: email,
                    namaFasilitas: namaFasilitas,
                    kodeFasilitas: kodeFasilitas,
                    kategori: kategori,
                    tanggal: tanggal,
                    waktu: waktu,
                    keperluan: keperluan,
                    status: status,
                    alasanPenolakan: alasanPenolakan,
                    berkasUrl: berkasUrl,
                    diajukanPada: diajukanPada
                };
                this.modalDetailOpen = true;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            }
        };
    }
</script>
@endsection
