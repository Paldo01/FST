@extends('layouts.app')

@section('title', 'Katalog Ruang - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" 
     x-data="{ 
         showBookingModal: false, 
         selectedRoomId: null,
         currentRoom: {
             id: null,
             nama: '',
             kapasitas: '',
             kategori: '',
             fasilitas: '',
             lokasi: '',
             gambar: ''
         },
         tanggalPeminjaman: '{{ request('tanggal', now()->format('Y-m-d')) }}',
         jamMulai: '08:00',
         jamSelesai: '12:00',
         jumlahPeserta: '',
         keperluan: '',
         fileName: '',
         checkingAvailability: false,
         availabilityStatus: null,
         availabilityMessage: '',
         bukaModalBooking(room) {
             this.currentRoom = room;
             this.selectedRoomId = room.id;
             this.showBookingModal = true;
             this.fileName = '';
             this.availabilityStatus = null;
             this.checkAvailability();
             this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
         },
         tutupModalBooking() {
             this.showBookingModal = false;
         },
         checkAvailability() {
             if (!this.selectedRoomId || !this.tanggalPeminjaman || !this.jamMulai || !this.jamSelesai) {
                 this.availabilityStatus = null;
                 return;
             }
             if (this.jamSelesai <= this.jamMulai) {
                 this.availabilityStatus = 'unavailable';
                 this.availabilityMessage = 'Jam selesai harus lebih akhir dari jam mulai.';
                 this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                 return;
             }
             this.checkingAvailability = true;
             fetch('{{ route('api.cek_ketersediaan') }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({
                     fasilitas_id: this.selectedRoomId,
                     tanggal_peminjaman: this.tanggalPeminjaman,
                     jam_mulai: this.jamMulai,
                     jam_selesai: this.jamSelesai
                 })
             })
             .then(res => res.json())
             .then(data => {
                 this.checkingAvailability = false;
                 if (data.tersedia) {
                     this.availabilityStatus = 'available';
                     this.availabilityMessage = data.pesan || 'Fasilitas masih tersedia pada tanggal dan waktu yang dipilih';
                 } else {
                     this.availabilityStatus = 'unavailable';
                     this.availabilityMessage = data.pesan || 'Fasilitas sudah terpakai pada rentang tanggal dan waktu tersebut';
                 }
                 this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
             })
             .catch(err => {
                 this.checkingAvailability = false;
                 this.availabilityStatus = null;
             });
         }
     }">

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
                        'lokasi' => $f->lokasi,
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
                            <button type="button"
                                    @click="bukaModalBooking({{ json_encode($modalPayload) }})" 
                                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs font-bold rounded-xl shadow-2xs transition-colors cursor-pointer">
                                <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                                <span>Ajukan Pinjam</span>
                            </button>
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

    <!-- Modal Ajukan Peminjaman (Terintegrasi Penuh di Katalog Ruang) -->
    <div x-show="showBookingModal" 
         x-cloak
         @keydown.escape.window="tutupModalBooking()"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop Blur -->
        <div x-show="showBookingModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" 
             @click="tutupModalBooking()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showBookingModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-5xl border border-gray-100 p-6 sm:p-8"
                 @click.stop>
                
                <!-- Form Peminjaman Sesuai Mockup -->
                <form action="{{ route('peminjaman.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                        
                        <!-- Kolom Kiri: Input Formulir -->
                        <div class="space-y-4">
                            <!-- Hidden ID Fasilitas (Sudah otomatis terpilih dari card) -->
                            <input type="hidden" name="fasilitas_id" :value="selectedRoomId">

                            <!-- 1. Tanggal Peminjaman -->
                            <div>
                                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-900 mb-1.5">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                    <span>Tanggal peminjaman</span>
                                </label>
                                <div class="relative">
                                    <input type="date" 
                                           name="tanggal_peminjaman" 
                                           x-model="tanggalPeminjaman" 
                                           @change="checkAvailability()" 
                                           min="{{ now()->format('Y-m-d') }}" 
                                           required 
                                           style="outline: none !important;"
                                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] cursor-pointer transition-all">
                                </div>
                            </div>

                            <!-- 2. Jam Mulai & Jam Selesai -->
                            <div class="grid grid-cols-2 gap-3.5">
                                <div>
                                    <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-900 mb-1.5">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                        <span>Jam mulai</span>
                                    </label>
                                    <input type="time" 
                                           name="jam_mulai" 
                                           x-model="jamMulai" 
                                           @change="checkAvailability()" 
                                           required 
                                           style="outline: none !important;"
                                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] cursor-pointer transition-all">
                                </div>
                                <div>
                                    <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-900 mb-1.5">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                        <span>Jam selesai</span>
                                    </label>
                                    <input type="time" 
                                           name="jam_selesai" 
                                           x-model="jamSelesai" 
                                           @change="checkAvailability()" 
                                           required 
                                           style="outline: none !important;"
                                           class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] cursor-pointer transition-all">
                                </div>
                            </div>

                            <!-- 3. Jumlah Pengguna/Peserta -->
                            <div>
                                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-900 mb-1.5">
                                    <i data-lucide="users" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                    <span>Jumlah pengguna/peserta</span>
                                </label>
                                <div class="flex items-stretch rounded-xl border border-gray-200 overflow-hidden bg-white focus-within:ring-2 focus-within:ring-[#801414]/20 focus-within:border-[#801414] transition-all">
                                    <input type="number" 
                                           name="jumlah_peserta" 
                                           x-model="jumlahPeserta" 
                                           placeholder="50" 
                                           min="1" 
                                           style="outline: none !important;"
                                           class="w-full px-4 py-2.5 text-xs sm:text-sm font-bold text-gray-800 focus:outline-none bg-transparent">
                                    <span class="bg-[#E5E7EB] text-gray-800 font-bold px-5 py-2.5 text-xs sm:text-sm flex items-center justify-center select-none shrink-0 border-l border-gray-200">
                                        Orang
                                    </span>
                                </div>
                            </div>

                            <!-- 4. Keperluan -->
                            <div>
                                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-900 mb-1.5">
                                    <i data-lucide="file-text" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                    <span>Keperluan</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           name="tujuan_peminjaman" 
                                           x-model="keperluan" 
                                           maxlength="200" 
                                           placeholder="Tuliskan tujuan atau keperluan peminjaman ruangan..." 
                                           required 
                                           style="outline: none !important;"
                                           class="w-full pl-4 pr-16 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] transition-all">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[11px] font-bold text-gray-400 font-mono select-none" 
                                          x-text="(keperluan ? keperluan.length : 0) + '/200'">0/200</span>
                                </div>
                            </div>

                            <!-- 5. Berkas Persyaratan -->
                            <div>
                                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-900 mb-1.5">
                                    <i data-lucide="paperclip" class="w-4 h-4 text-gray-800 shrink-0"></i>
                                    <span>Berkas Persyaratan (Surat Peminjaman)</span>
                                </label>
                                <div class="flex items-center gap-3 px-3 py-2 bg-white border border-gray-200 rounded-xl focus-within:ring-2 focus-within:ring-[#801414]/20 focus-within:border-[#801414] transition-all">
                                    <label class="cursor-pointer shrink-0 px-3.5 py-1.5 bg-[#FEE2E2] hover:bg-[#FCD8D8] text-[#801414] text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 select-none">
                                        <span>Pilih File</span>
                                        <input type="file" 
                                               name="berkas_persyaratan" 
                                               accept=".pdf" 
                                               @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" 
                                               class="hidden">
                                    </label>
                                    <span class="text-xs text-gray-500 truncate flex-1 font-medium" 
                                          x-text="fileName || 'Tidak ada file yang dipilih'">
                                        Tidak ada file yang dipilih
                                    </span>
                                    <button type="button" 
                                            x-show="fileName" 
                                            @click="fileName = ''; $el.closest('div').querySelector('input[type=file]').value = ''" 
                                            class="text-gray-400 hover:text-red-500 p-1 cursor-pointer" 
                                            title="Hapus file">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1 pl-1">Opsional. Format PDF maksimal 5MB.</p>
                            </div>

                        </div>

                        <!-- Kolom Kanan: Detail Fasilitas & Aksi -->
                        <div class="flex flex-col justify-between h-full space-y-5">
                            
                            <div class="space-y-3">
                                <!-- Header Kolom Kanan & Tombol Close X -->
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-black text-gray-900 tracking-tight">
                                        Detail Fasilitas
                                    </h3>
                                    <button type="button" 
                                            @click="tutupModalBooking()"
                                            class="text-gray-400 hover:text-gray-700 p-1.5 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer"
                                            title="Tutup">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </div>

                                <!-- Card Beige Soft Sesuai Mockup -->
                                <div class="bg-[#FAF7F7] border border-gray-200/90 rounded-2xl p-5 space-y-4 shadow-2xs">
                                    <!-- Info Ruang (Foto di Kiri, Specs di Kanan) -->
                                    <div class="flex items-start gap-4">
                                        <!-- Thumbnail Ruang -->
                                        <div class="w-32 sm:w-36 h-28 rounded-xl overflow-hidden shrink-0 border border-gray-200/90 bg-gray-100 shadow-2xs">
                                            <img :src="currentRoom.gambar" 
                                                 :alt="currentRoom.nama" 
                                                 class="w-full h-full object-cover">
                                        </div>

                                        <!-- Detail Specs -->
                                        <div class="flex-1 min-w-0 space-y-2 text-xs">
                                            <h4 class="text-base font-black text-gray-900 tracking-tight leading-tight" x-text="currentRoom.nama">Aula</h4>
                                            
                                            <div class="space-y-1.5 text-xs text-gray-700">
                                                <div class="grid grid-cols-[88px_8px_1fr] items-baseline">
                                                    <span class="flex items-center gap-1.5 text-gray-600 font-bold whitespace-nowrap">
                                                        <i data-lucide="users" class="w-3.5 h-3.5 text-gray-600 shrink-0"></i>
                                                        <span>Kapasitas</span>
                                                    </span>
                                                    <span class="font-bold text-gray-800 text-center">:</span>
                                                    <span class="font-bold text-gray-900 pl-0.5 whitespace-nowrap" x-text="currentRoom.kapasitas">200 Orang</span>
                                                </div>

                                                <div class="grid grid-cols-[88px_8px_1fr] items-baseline">
                                                    <span class="flex items-center gap-1.5 text-gray-600 font-bold whitespace-nowrap">
                                                        <i data-lucide="building" class="w-3.5 h-3.5 text-gray-600 shrink-0"></i>
                                                        <span>Jenis ruang</span>
                                                    </span>
                                                    <span class="font-bold text-gray-800 text-center">:</span>
                                                    <span class="font-bold text-gray-900 pl-0.5 whitespace-nowrap" x-text="currentRoom.kategori">Ruangan</span>
                                                </div>

                                                <div class="grid grid-cols-[88px_8px_1fr] items-baseline">
                                                    <span class="flex items-center gap-1.5 text-gray-600 font-bold whitespace-nowrap">
                                                        <i data-lucide="settings" class="w-3.5 h-3.5 text-gray-600 shrink-0"></i>
                                                        <span>Fasilitas</span>
                                                    </span>
                                                    <span class="font-bold text-gray-800 text-center">:</span>
                                                    <span class="font-bold text-gray-900 pl-0.5 leading-snug line-clamp-2" x-text="currentRoom.fasilitas" :title="currentRoom.fasilitas">Infokus, AC</span>
                                                </div>

                                                <div class="grid grid-cols-[88px_8px_1fr] items-baseline">
                                                    <span class="flex items-center gap-1.5 text-gray-600 font-bold whitespace-nowrap">
                                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-600 shrink-0"></i>
                                                        <span>Lokasi</span>
                                                    </span>
                                                    <span class="font-bold text-gray-800 text-center">:</span>
                                                    <span class="font-bold text-gray-900 pl-0.5 leading-snug truncate" x-text="currentRoom.lokasi">Gedung FST</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Feedback Live Pengecekan Ketersediaan Jadwal -->
                                    <!-- 1. Jadwal Tersedia (Green) -->
                                    <div x-show="!checkingAvailability && availabilityStatus === 'available'" 
                                         class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 flex items-start gap-2.5">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-xs font-bold text-emerald-950">Jadwal tersedia</p>
                                            <p class="text-[11px] text-emerald-700 font-medium leading-snug" x-text="availabilityMessage">Fasilitas masih tersedia pada tanggal dan waktu yang dipilih</p>
                                        </div>
                                    </div>

                                    <!-- 2. Jadwal Tidak Tersedia (Red) -->
                                    <div x-show="!checkingAvailability && availabilityStatus === 'unavailable'" 
                                         class="bg-red-50 border border-red-200 rounded-xl p-3 flex items-start gap-2.5">
                                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-xs font-bold text-red-950">Jadwal tidak tersedia</p>
                                            <p class="text-[11px] text-red-700 font-medium leading-snug" x-text="availabilityMessage">Fasilitas sudah terpakai pada rentang tanggal dan waktu tersebut</p>
                                        </div>
                                    </div>

                                    <!-- 3. Loading Pengecekan (Blue) -->
                                    <div x-show="checkingAvailability" 
                                         class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-start gap-2.5">
                                        <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 animate-spin shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-xs font-bold text-blue-950">Memeriksa ketersediaan jadwal...</p>
                                            <p class="text-[11px] text-blue-700 font-medium leading-snug">Mohon tunggu sebentar</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi di Bawah Card Kanan (Sesuai Mockup: Batal & Ajukan Peminjaman) -->
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" 
                                        @click="tutupModalBooking()"
                                        class="px-7 py-2.5 bg-white border border-[#DC2626] hover:bg-red-50 text-[#DC2626] text-xs sm:text-sm font-bold rounded-xl transition-all cursor-pointer">
                                    Batal
                                </button>

                                <button type="submit" 
                                        :disabled="availabilityStatus === 'unavailable'"
                                        class="px-6 py-2.5 bg-[#DC2626] hover:bg-[#B91C1C] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    <span>Ajukan Peminjaman</span>
                                </button>
                            </div>

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

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
