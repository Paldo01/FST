@extends('layouts.app')

@section('title', 'Detail Ruang - ' . $fasilitas->nama_fasilitas . ' - Sistem Peminjaman FST')

@section('content')
<div class="space-y-5 pb-12 pt-1 sm:pt-2" 
     x-data="{ 
         activeImage: '{{ $gallery[0] }}',
         showBookingModal: {{ request('ajukan') ? 'true' : 'false' }},
         semuaRuangan: {{ json_encode($semuaRuangan) }},
         selectedRoomId: '{{ $fasilitas->id }}',
         currentRoom: {
             id: {{ $fasilitas->id }},
             nama: '{{ addslashes($fasilitas->nama_fasilitas) }}',
             kapasitas: '{{ addslashes($fasilitas->kapasitas_formatted) }}',
             kategori: '{{ addslashes($fasilitas->kategori ? $fasilitas->kategori->nama_kategori : "Aula") }}',
             fasilitas: '{{ addslashes($fasilitas->deskripsi ?: "Infokus,AC") }}',
             lokasi: '{{ addslashes($fasilitas->lokasi) }}',
             gambar: '{{ $gallery[0] }}'
         },
         tanggalPeminjaman: '{{ old('tanggal_peminjaman', $selectedDate->format('Y-m-d')) }}',
         jamMulai: '{{ old('jam_mulai', '08:00') }}',
         jamSelesai: '{{ old('jam_selesai', '12:00') }}',
         jumlahPeserta: '{{ old('jumlah_peserta', '') }}',
         keperluan: '{{ old('tujuan_peminjaman', '') }}',
         fileName: '',
         checkingAvailability: false,
         availabilityStatus: null,
         availabilityMessage: '',
         onRoomChange() {
             const found = this.semuaRuangan.find(r => r.id == this.selectedRoomId);
             if (found) {
                 this.currentRoom = found;
             }
             this.checkAvailability();
         },
         checkAvailability() {
             if (!this.tanggalPeminjaman || !this.jamMulai || !this.jamSelesai) {
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
     }"
     x-init="checkAvailability(); if ({{ request('ajukan') ? 'true' : 'false' }}) { $nextTick(() => { if (window.lucide) window.lucide.createIcons(); }); }">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div>
        <a href="{{ route('katalog.ruang') }}" 
           class="text-xs font-bold text-[#801414] hover:underline inline-flex items-center gap-1.5 transition-colors">
            <span>←</span>
            <span>Kembali ke katalog ruang</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-1">
            Detail Ruang
        </h1>
    </div>

    <!-- Main Grid: 2 Kolom (Foto di Kiri, Info & Jadwal di Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Kolom Kiri: Galeri Foto Ruangan (5 atau 6 Kolom) -->
        <div class="lg:col-span-7 space-y-3.5">
            <!-- Foto Utama Besar -->
            <div class="rounded-2xl sm:rounded-3xl overflow-hidden aspect-[16/10] bg-gray-100 border border-gray-200/90 shadow-2xs relative">
                <img :src="activeImage" 
                     alt="{{ $fasilitas->nama_fasilitas }}" 
                     class="w-full h-full object-cover transition-all duration-300">
                
                <div class="absolute top-3.5 right-3.5">
                    @if($fasilitas->status === 'tersedia')
                        <span class="px-3 py-1 bg-white/95 backdrop-blur-xs text-emerald-700 text-xs font-bold rounded-full shadow-sm flex items-center gap-1.5 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Tersedia</span>
                        </span>
                    @else
                        <span class="px-3 py-1 bg-white/95 backdrop-blur-xs text-amber-700 text-xs font-bold rounded-full shadow-sm flex items-center gap-1.5 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>{{ ucfirst($fasilitas->status) }}</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- 3 Foto Thumbnail di Bawah (Sesuai Mockup) -->
            <div class="grid grid-cols-3 gap-3">
                @foreach($gallery as $index => $img)
                    <button type="button" 
                            @click="activeImage = '{{ $img }}'" 
                            class="rounded-xl sm:rounded-2xl overflow-hidden aspect-video bg-gray-100 border-2 transition-all duration-200 cursor-pointer"
                            :class="activeImage === '{{ $img }}' ? 'border-[#801414] shadow-xs scale-[0.98]' : 'border-transparent opacity-75 hover:opacity-100'">
                        <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Kolom Kanan: Card Info Ruang & Jadwal Penggunaan -->
        <div class="lg:col-span-5 bg-white rounded-2xl sm:rounded-3xl border border-gray-200/90 shadow-2xs p-6 sm:p-7 space-y-5">
            
            <!-- Judul Ruangan -->
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                    {{ $fasilitas->nama_fasilitas }}
                </h2>
                <p class="text-xs text-gray-400 font-mono mt-0.5">Kode: {{ $fasilitas->kode_fasilitas }}</p>
            </div>

            <!-- Detail Spesifikasi (Sesuai Mockup) -->
            <div class="space-y-3 text-xs sm:text-sm text-gray-800 font-medium pt-1">
                <!-- 1. Kapasitas -->
                <div class="flex items-center gap-3">
                    <div class="w-7 text-center shrink-0">
                        <i data-lucide="users" class="w-5 h-5 text-gray-700 inline-block"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="w-24 text-gray-600 font-bold shrink-0">Kapasitas</span>
                        <span class="font-bold text-gray-900">: {{ $fasilitas->kapasitas_formatted }}</span>
                    </div>
                </div>

                <!-- 2. Jenis Ruang -->
                <div class="flex items-center gap-3">
                    <div class="w-7 text-center shrink-0">
                        <i data-lucide="building" class="w-5 h-5 text-gray-700 inline-block"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="w-24 text-gray-600 font-bold shrink-0">Jenis Ruang</span>
                        <span class="font-bold text-gray-900">: {{ $fasilitas->kategori ? $fasilitas->kategori->nama_kategori : 'Aula' }}</span>
                    </div>
                </div>

                <!-- 3. Fasilitas -->
                <div class="flex items-start gap-3">
                    <div class="w-7 text-center shrink-0 pt-0.5">
                        <i data-lucide="settings" class="w-5 h-5 text-gray-700 inline-block"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="w-24 text-gray-600 font-bold shrink-0">Fasilitas</span>
                        <span class="font-bold text-gray-900">: {{ $fasilitas->deskripsi ?: 'Infocus, Sound System, AC' }}</span>
                    </div>
                </div>

                <!-- 4. Lokasi -->
                <div class="flex items-center gap-3">
                    <div class="w-7 text-center shrink-0">
                        <i data-lucide="map-pin" class="w-5 h-5 text-gray-700 inline-block"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="w-24 text-gray-600 font-bold shrink-0">Lokasi</span>
                        <span class="font-bold text-gray-900">: {{ $fasilitas->lokasi }}</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-100 pt-1"></div>

            <!-- Bagian Jadwal Penggunaan (Sesuai Mockup) -->
            <div class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <h3 class="text-sm sm:text-base font-black text-gray-900">
                        Jadwal Penggunaan
                    </h3>

                    <!-- Pilihan Tanggal Dropdown / Picker -->
                    <form method="GET" action="{{ route('katalog.ruang.detail', $fasilitas->id) }}" id="jadwalDateForm" class="shrink-0">
                        <div class="relative">
                            <i data-lucide="calendar" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-600 pointer-events-none"></i>
                            <input type="date" 
                                   name="tanggal" 
                                   value="{{ $selectedDate->format('Y-m-d') }}" 
                                   onchange="document.getElementById('jadwalDateForm').submit()"
                                   class="pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] cursor-pointer">
                        </div>
                    </form>
                </div>

                <!-- Tabel Jadwal (Sesuai Mockup) -->
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-xs text-left">
                        <thead class="text-[11px] font-bold text-gray-700 uppercase bg-[#F8FAFC] border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-3 py-2.5">Tanggal</th>
                                <th scope="col" class="px-3 py-2.5">Waktu</th>
                                <th scope="col" class="px-3 py-2.5">Keperluan</th>
                                <th scope="col" class="px-3 py-2.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($jadwalSlots as $slot)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-3 py-2.5 font-medium text-gray-800 whitespace-nowrap">
                                        {{ $slot['tanggal'] }}
                                    </td>
                                    <td class="px-3 py-2.5 font-mono text-gray-600 whitespace-nowrap">
                                        {{ $slot['waktu'] }}
                                    </td>
                                    <td class="px-3 py-2.5 font-medium text-gray-800">
                                        {{ $slot['keperluan'] }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                        @if($slot['status'] === 'Terpakai')
                                            <span class="inline-flex items-center gap-1 text-xs font-bold text-[#DC2626]">
                                                <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                                                <span>Terpakai</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <span>Tersedia</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tombol Aksi: Ajukan Peminjaman Ruang -->
            <div class="pt-3">
                @if($fasilitas->status === 'tersedia')
                    <button type="button" 
                            @click="showBookingModal = true; $nextTick(() => { if (window.lucide) window.lucide.createIcons(); })" 
                            class="w-full py-3 px-4 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                        <span>Ajukan Peminjaman Ruang Ini</span>
                    </button>
                @else
                    <button type="button" disabled 
                            class="w-full py-3 px-4 bg-gray-100 text-gray-400 text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl cursor-not-allowed flex items-center justify-center gap-2">
                        <i data-lucide="slash" class="w-4 h-4"></i>
                        <span>Ruangan Sedang Tidak Tersedia</span>
                    </button>
                @endif
            </div>

        </div>

    </div>

    <!-- Modal Ajukan Peminjaman (Terintegrasi Penuh di Katalog Ruang) -->
    <div x-show="showBookingModal" 
         x-cloak
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
             @click="showBookingModal = false"></div>        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
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
                            <!-- Hidden ID Fasilitas (Sudah otomatis terpilih dari halaman detail ini) -->
                            <input type="hidden" name="fasilitas_id" value="{{ $fasilitas->id }}">

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
                                @error('tanggal_peminjaman')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                    @error('jam_mulai')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
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
                                    @error('jam_selesai')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
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
                                @error('jumlah_peserta')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                @error('tujuan_peminjaman')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 5. Berkas Persyaratan (Tetap Ada Sesuai Permintaan User) -->
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
                                @error('berkas_persyaratan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                            @click="showBookingModal = false"
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
                                                    <span class="font-bold text-gray-900 pl-0.5 leading-snug" x-text="currentRoom.lokasi">Gedung FST Lantai 3</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Box Sesuai Mockup -->
                                    <!-- 1. Jadwal Tersedia (Green) -->
                                    <div x-show="!checkingAvailability && (availabilityStatus === 'available' || availabilityStatus === null)" 
                                         class="bg-[#E6F4EA] border border-emerald-300/80 rounded-xl p-3 flex items-start gap-2.5">
                                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-xs font-bold text-emerald-950">Jadwal tersedia</p>
                                            <p class="text-[11px] text-emerald-800 font-medium leading-snug">Fasilitas masih tersedia pada tanggal dan waktu yang dipilih</p>
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
                                        @click="showBookingModal = false"
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
