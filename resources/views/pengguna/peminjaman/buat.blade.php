@extends('layouts.app')

@section('title', 'Ajukan Peminjaman - Sistem Peminjaman FST')

@section('content')
@php
    $semuaRuangan = $fasilitas->map(function($r) {
        $namaLower = strtolower($r->nama_fasilitas);
        if (str_contains($namaLower, 'aula')) {
            $img = asset('images/aula-fst.jpg');
        } elseif (str_contains($namaLower, 'lab') || str_contains($namaLower, 'komputer')) {
            $img = asset('images/lab-komputer.jpg');
        } else {
            $img = asset('images/ruang-kelas.jpg');
        }
        return [
            'id' => $r->id,
            'nama' => $r->nama_fasilitas,
            'kapasitas' => $r->kapasitas_formatted,
            'kategori' => $r->kategori ? $r->kategori->nama_kategori : 'Ruangan',
            'fasilitas' => $r->deskripsi ?: 'Infokus, AC',
            'lokasi' => $r->lokasi,
            'gambar' => $img,
        ];
    });
    $defaultRoom = $semuaRuangan->firstWhere('id', request('fasilitas', request('fasilitas_id'))) ?: $semuaRuangan->first();
@endphp

<div class="space-y-4 max-w-5xl mx-auto pb-12 pt-2"
     x-data="{
         semuaRuangan: {{ json_encode($semuaRuangan) }},
         selectedRoomId: '{{ $defaultRoom ? $defaultRoom['id'] : '' }}',
         currentRoom: {{ json_encode($defaultRoom ?: [
             'id' => 0,
             'nama' => '-',
             'kapasitas' => '-',
             'kategori' => '-',
             'fasilitas' => '-',
             'lokasi' => '-',
             'gambar' => asset('images/aula-fst.jpg')
         ]) }},
         tanggalPeminjaman: '{{ old('tanggal_peminjaman', now()->format('Y-m-d')) }}',
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
     }"
     x-init="checkAvailability(); $nextTick(() => { if (window.lucide) window.lucide.createIcons(); });">

    <!-- Navigasi Kembali ke Katalog Ruang -->
    <div class="flex items-center justify-between">
        <a href="{{ request('fasilitas') ? route('katalog.ruang.detail', request('fasilitas')) : route('katalog.ruang') }}" 
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-500 hover:text-[#801414] transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Katalog Ruang</span>
        </a>
    </div>

    <!-- Container Utama Formulir (Sesuai Mockup Desain) -->
    <div class="bg-white rounded-3xl border border-gray-200/90 shadow-2xs p-6 sm:p-8">
        
        <form action="{{ route('peminjaman.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                
                <!-- Kolom Kiri: Input Formulir -->
                <div class="space-y-4">
                    
                    <!-- 1. Fasilitas Dropdown -->
                    <div>
                        <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-900 mb-1.5">
                            <i data-lucide="monitor" class="w-4 h-4 text-gray-800 shrink-0"></i>
                            <span>Fasilitas</span>
                        </label>
                        <div class="relative">
                            <select name="fasilitas_id" 
                                    x-model="selectedRoomId" 
                                    @change="onRoomChange()" 
                                    style="outline: none !important;"
                                    class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-bold text-gray-800 appearance-none focus:outline-none focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] cursor-pointer transition-all">
                                @foreach($semuaRuangan as $r)
                                    <option value="{{ $r['id'] }}">{{ $r['nama'] }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-700 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                        @error('fasilitas_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Tanggal Peminjaman -->
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

                    <!-- 3. Jam Mulai & Jam Selesai -->
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

                    <!-- 4. Jumlah Pengguna/Peserta -->
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

                    <!-- 5. Keperluan -->
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

                    <!-- 6. Berkas Persyaratan (Tetap Ada Sesuai Permintaan User) -->
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
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">
                            Detail Fasilitas
                        </h3>

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
                        <a href="{{ route('katalog.ruang') }}" 
                           class="px-7 py-2.5 bg-white border border-[#DC2626] hover:bg-red-50 text-[#DC2626] text-xs sm:text-sm font-bold rounded-xl transition-all cursor-pointer text-center">
                            Batal
                        </a>

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
