@extends('layouts.app')

@section('title', 'Input Peminjaman Alat - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="inputPeminjamanAlatHandler()">

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.alat.index') }}" class="text-xs font-bold text-gray-500 hover:text-marun-800 flex items-center gap-1 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kelola Alat</span>
                </a>
                <span class="text-xs text-gray-300">/</span>
                <span class="text-xs font-bold text-marun-800">Input Peminjaman</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                Input Peminjaman Alat
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Pilih alat dari daftar katalog dan catat peminjaman untuk dosen atau mahasiswa
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pengembalian.index') }}" 
               class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl shadow-2xs transition-all flex items-center gap-1.5">
                <i data-lucide="package-check" class="w-4 h-4 text-emerald-600"></i>
                <span>Daftar Pengembalian</span>
            </a>
        </div>
    </div>

    <!-- Alert Error Validasi Global -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
            <div class="text-xs sm:text-sm text-red-800">
                <p class="font-bold">Terdapat beberapa kesalahan input:</p>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form Peminjaman Alat -->
    <form action="{{ route('admin.peminjaman.store') }}" method="POST" id="peminjamanAlatForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Kolom Kiri: Katalog Pemilihan Alat (Lg: 7 Kolom) -->
            <div class="lg:col-span-7 space-y-4">
                
                <div class="bg-white rounded-3xl border border-gray-200/90 shadow-2xs p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-marun-50 text-marun-800 flex items-center justify-center font-black">
                                <i data-lucide="monitor" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-gray-900 leading-tight">1. Pilih Alat yang Dipinjam</h2>
                                <p class="text-xs text-gray-500">Klik salah satu kartu alat di bawah untuk memilih</p>
                            </div>
                        </div>

                        <!-- Badge Alat Terpilih -->
                        <template x-if="selectedAlat">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-marun-50 text-marun-800 border border-marun-200 self-start sm:self-auto">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-marun-700"></i>
                                <span x-text="selectedAlat.nama"></span>
                            </span>
                        </template>
                    </div>

                    <!-- Filter Pencarian & Kategori Alat -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                            <input type="text" x-model="searchKeyword" placeholder="Cari nama atau kode alat..."
                                   class="w-full pl-10 pr-4 py-2 bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-marun-700 focus:border-marun-700 transition-all">
                        </div>

                        <div class="relative">
                            <select x-model="kategoriFilter"
                                    class="w-full px-3 py-2 bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-marun-700 focus:border-marun-700 transition-all cursor-pointer">
                                <option value="semua">Semua Kategori</option>
                                <template x-for="kat in daftarKategori" :key="kat">
                                    <option :value="kat" x-text="kat"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Input Tersembunyi untuk ID Fasilitas -->
                    <input type="hidden" name="fasilitas_id" :value="selectedAlatId" required>

                    <!-- Grid Kartu Alat Sesuai Mockup -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[580px] overflow-y-auto pr-1">
                        <template x-for="item in filteredAlatList" :key="item.id">
                            <div @click="selectTool(item)"
                                 class="relative rounded-2xl border transition-all duration-150 p-3.5 flex flex-col justify-between cursor-pointer group select-none text-left"
                                 :class="selectedAlatId == item.id 
                                     ? 'bg-marun-50/50 border-marun-800 ring-2 ring-marun-800 shadow-sm' 
                                     : (item.isBorrowed || item.status !== 'tersedia' 
                                         ? 'bg-gray-50/80 border-gray-200 opacity-60 cursor-not-allowed' 
                                         : 'bg-white border-gray-200 hover:border-marun-300 hover:shadow-xs hover:bg-gray-50/50')">
                                
                                <div>
                                    <!-- Header Kartu: Gambar Mini & Badge Status -->
                                    <div class="h-28 w-full relative overflow-hidden rounded-xl bg-gray-50 flex items-center justify-center p-2 mb-3 border border-gray-100">
                                        <img :src="item.gambar" :alt="item.nama" class="w-full h-full object-contain group-hover:scale-105 transition-transform">
                                        
                                        <!-- Radio Checkmark Indicator -->
                                        <div class="absolute top-2 left-2 z-10">
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black border transition-all"
                                                  :class="selectedAlatId == item.id ? 'bg-marun-800 text-white border-marun-800' : 'bg-white text-transparent border-gray-300'">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                            </span>
                                        </div>

                                        <!-- Badge Status -->
                                        <div class="absolute top-2 right-2 z-10">
                                            <template x-if="item.isBorrowed">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    <span>Dipinjam</span>
                                                </span>
                                            </template>
                                            <template x-if="!item.isBorrowed && item.status === 'tersedia'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    <span>Tersedia</span>
                                                </span>
                                            </template>
                                            <template x-if="!item.isBorrowed && item.status !== 'tersedia'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                    <span x-text="item.status"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Informasi Alat -->
                                    <h4 class="font-bold text-gray-900 text-sm leading-snug group-hover:text-marun-800 transition-colors" x-text="item.nama"></h4>
                                    
                                    <div class="mt-2 space-y-1 text-[11px] text-gray-600">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400">Kode:</span>
                                            <span class="font-mono font-bold text-gray-800" x-text="item.kode"></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400">Kategori:</span>
                                            <span class="font-semibold text-gray-700" x-text="item.kategori"></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400">Kondisi:</span>
                                            <span class="font-semibold" 
                                                  :class="item.kondisi.toLowerCase() === 'baik' ? 'text-emerald-700' : 'text-amber-700'" 
                                                  x-text="item.kondisi"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Pilih Cepat -->
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    <template x-if="!item.isBorrowed && item.status === 'tersedia'">
                                        <button type="button" 
                                                class="w-full py-1.5 px-3 rounded-lg text-xs font-bold transition-all text-center"
                                                :class="selectedAlatId == item.id 
                                                    ? 'bg-marun-800 text-white' 
                                                    : 'bg-gray-100 text-gray-700 group-hover:bg-marun-100 group-hover:text-marun-800'">
                                            <span x-text="selectedAlatId == item.id ? 'Alat Dipilih' : 'Pilih Alat Ini'"></span>
                                        </button>
                                    </template>
                                    <template x-if="item.isBorrowed || item.status !== 'tersedia'">
                                        <span class="block text-center text-[10px] font-bold text-gray-400 py-1">
                                            Tidak Dapat Dipinjam
                                        </span>
                                    </template>
                                </div>

                            </div>
                        </template>

                        <!-- Empty State Pencarian -->
                        <template x-if="filteredAlatList.length === 0">
                            <div class="col-span-full py-12 text-center text-gray-500">
                                <i data-lucide="package-x" class="w-8 h-8 mx-auto text-gray-300 mb-2"></i>
                                <p class="text-sm font-bold text-gray-700">Alat Tidak Ditemukan</p>
                                <p class="text-xs text-gray-400 mt-0.5">Sesuaikan kata kunci atau filter kategori</p>
                            </div>
                        </template>
                    </div>

                </div>

            </div>

            <!-- Kolom Kanan: Identitas Peminjam & Form Pengambilan (Lg: 5 Kolom) -->
            <div class="lg:col-span-5 space-y-4">
                
                <div class="bg-white rounded-3xl border border-gray-200/90 shadow-2xs p-5 sm:p-6 space-y-5">
                    
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-xl bg-marun-50 text-marun-800 flex items-center justify-center font-black">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-gray-900 leading-tight">2. Data Peminjam & Waktu</h2>
                            <p class="text-xs text-gray-500">Catat identitas peminjam dan keperluannya</p>
                        </div>
                    </div>

                    <!-- Ringkasan Alat Terpilih -->
                    <div class="p-3.5 rounded-2xl border" 
                         :class="selectedAlat ? 'bg-marun-50/50 border-marun-200' : 'bg-gray-50 border-gray-200 text-center'">
                        <template x-if="selectedAlat">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl bg-white p-1 border border-gray-100 shrink-0 flex items-center justify-center overflow-hidden">
                                    <img :src="selectedAlat.gambar" :alt="selectedAlat.nama" class="w-full h-full object-contain">
                                </div>
                                <div class="min-w-0 flex-1 text-left">
                                    <p class="text-[10px] font-bold text-marun-700 uppercase tracking-wider">Alat Terpilih</p>
                                    <h4 class="font-black text-gray-900 text-sm truncate" x-text="selectedAlat.nama"></h4>
                                    <p class="text-[11px] text-gray-500 font-mono" x-text="selectedAlat.kode + ' • ' + selectedAlat.kategori"></p>
                                </div>
                                <button type="button" @click="selectedAlatId = ''; selectedAlat = null" class="text-gray-400 hover:text-red-600 p-1">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </template>
                        <template x-if="!selectedAlat">
                            <div class="py-2 text-xs text-gray-500 font-medium">
                                <i data-lucide="info" class="w-4 h-4 inline-block text-marun-700 mr-1"></i>
                                Silakan pilih salah satu alat di katalog sebelah kiri.
                            </div>
                        </template>
                    </div>

                    <!-- Pilih Peminjam (Autocomplete Dosen / Mahasiswa) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-800">
                            Nama Peminjam (Dosen / Mahasiswa) <span class="text-red-500">*</span>
                        </label>

                        <div class="relative" @click.away="showUserDropdown = false">
                            <input type="hidden" name="pengguna_id" :value="selectedUserId" required>
                            
                            <div class="relative">
                                <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                <input type="text" 
                                       x-model="searchUser"
                                       @focus="showUserDropdown = true"
                                       @input="showUserDropdown = true; selectedUserId = ''"
                                       placeholder="Ketik NIM / NIP atau nama peminjam..."
                                       autocomplete="off"
                                       class="w-full pl-10 pr-9 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border rounded-xl text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-marun-700 focus:border-marun-700 transition-all"
                                       :class="selectedUserId ? 'border-emerald-300 ring-1 ring-emerald-200' : 'border-gray-200'">
                                
                                <button type="button" x-show="searchUser !== ''" 
                                        @click="searchUser = ''; selectedUserId = ''; selectedUser = null; showUserDropdown = true;" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <!-- Dropdown List Hasil Pencarian -->
                            <div x-show="showUserDropdown && filteredUserList.length > 0" 
                                 x-transition 
                                 class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl max-h-52 overflow-y-auto">
                                <template x-for="user in filteredUserList" :key="user.id">
                                    <div @click="selectUser(user)"
                                         class="px-3.5 py-2.5 text-xs text-gray-800 hover:bg-marun-50 hover:text-marun-900 cursor-pointer border-b border-gray-50 last:border-0 flex items-center justify-between">
                                        <div class="min-w-0 pr-2">
                                            <p class="font-bold truncate" x-text="user.nama"></p>
                                            <p class="text-[10px] text-gray-500 font-mono" x-text="user.nomor_identitas"></p>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize shrink-0"
                                              :class="user.jenis.toLowerCase() === 'dosen' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800'"
                                              x-text="user.jenis"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Alert Tidak Ditemukan -->
                            <div x-show="showUserDropdown && searchUser !== '' && filteredUserList.length === 0 && !selectedUserId" 
                                 class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg p-3 text-center text-xs text-gray-500">
                                Pengguna dengan NIM / NIP atau nama tersebut tidak ditemukan.
                            </div>
                        </div>

                        <!-- Card Detail User Terpilih -->
                        <template x-if="selectedUser">
                            <div class="mt-2 p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                                    <div>
                                        <span class="font-bold text-emerald-950" x-text="selectedUser.nama"></span>
                                        <span class="text-emerald-700 text-[11px]" x-text="' (' + selectedUser.nomor_identitas + ')'"></span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full capitalize"
                                      :class="selectedUser.jenis.toLowerCase() === 'dosen' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-200/80 text-emerald-900'"
                                      x-text="selectedUser.jenis"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Waktu Pengambilan (Tanggal & Jam) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-800">
                            Waktu Pengambilan (Tanggal &amp; Jam) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="datetime-local" 
                                   name="tanggal_mulai" 
                                   x-model="tanggalMulai" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-marun-700 focus:border-marun-700 transition-all cursor-pointer">
                        </div>
                        <p class="text-[11px] text-gray-500 leading-snug">
                            Waktu pengembalian akan tercatat otomatis saat alat diserahkan kembali di menu <strong>Pengembalian Alat</strong>.
                        </p>
                    </div>

                    <!-- Tujuan / Keperluan Peminjaman -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-800">
                            Keperluan / Tujuan Peminjaman <span class="text-red-500">*</span>
                        </label>
                        <textarea name="tujuan_peminjaman" 
                                  rows="3" 
                                  required 
                                  placeholder="Contoh: Praktikum Pemrograman Web di Lab Komputer 2, Acara Seminar Mahasiswa..."
                                  class="w-full p-3 bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-marun-700 focus:border-marun-700 transition-all resize-none">{{ old('tujuan_peminjaman') }}</textarea>
                    </div>

                    <!-- Tombol Simpan & Batal -->
                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <a href="{{ route('admin.alat.index') }}" 
                           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all">
                            Batal
                        </a>
                        <button type="submit" 
                                :disabled="!selectedAlatId || !selectedUserId"
                                class="px-6 py-2.5 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Peminjaman</span>
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function inputPeminjamanAlatHandler() {
        const rawAlat = [
            @foreach($fasilitas as $f)
                @php
                    $isBorrowed = in_array($f->id, $sedangDipinjamIds);
                    
                    $imgUrl = null;
                    if ($f->gambar && file_exists(public_path($f->gambar))) {
                        $imgUrl = asset($f->gambar);
                    } elseif ($f->gambar && file_exists(public_path('storage/' . $f->gambar))) {
                        $imgUrl = asset('storage/' . $f->gambar);
                    } else {
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

                    $kategoriStr = $f->kategori ? $f->kategori->nama_kategori : 'Peralatan';
                    $kondisiStr = ucfirst(str_replace('_', ' ', $f->kondisi_awal ?: 'Baik'));
                @endphp
                {
                    id: {{ $f->id }},
                    nama: '{{ addslashes($f->nama_fasilitas) }}',
                    kode: '{{ addslashes($f->kode_fasilitas) }}',
                    kategori: '{{ addslashes($kategoriStr) }}',
                    kondisi: '{{ addslashes($kondisiStr) }}',
                    status: '{{ addslashes($f->status) }}',
                    isBorrowed: {{ $isBorrowed ? 'true' : 'false' }},
                    gambar: '{{ $imgUrl }}',
                    searchStr: '{{ addslashes(strtolower($f->nama_fasilitas . ' ' . $f->kode_fasilitas . ' ' . $kategoriStr)) }}'
                },
            @endforeach
        ];

        const rawPengguna = [
            @foreach($pengguna as $p)
                {
                    id: {{ $p->id }},
                    nama: '{{ addslashes($p->nama_lengkap) }}',
                    nomor_identitas: '{{ addslashes($p->nomor_identitas) }}',
                    jenis: '{{ addslashes(ucfirst($p->jenis_pengguna ?: "Mahasiswa")) }}',
                    label: '{{ addslashes($p->nama_lengkap . " (" . $p->nomor_identitas . ") - " . ucfirst($p->jenis_pengguna ?: "Mahasiswa")) }}',
                    keyword: '{{ addslashes(strtolower($p->nama_lengkap . " " . $p->nomor_identitas)) }}'
                },
            @endforeach
        ];

        const defaultAlatId = '{{ old('fasilitas_id', $selectedAlatId ?? '') }}';
        const initialSelectedAlat = rawAlat.find(a => a.id == defaultAlatId) || null;

        const defaultUserId = '{{ old('pengguna_id', '') }}';
        const initialSelectedUser = rawPengguna.find(u => u.id == defaultUserId) || null;

        // Default tanggal mulai sekarang (format YYYY-MM-DDTHH:mm)
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const defaultTanggalMulai = '{{ old('tanggal_mulai') }}' || now.toISOString().slice(0, 16);

        // Kategori unik
        const kategoriSet = [...new Set(rawAlat.map(a => a.kategori))];

        return {
            alatList: rawAlat,
            penggunaList: rawPengguna,
            daftarKategori: kategoriSet,
            searchKeyword: '',
            kategoriFilter: 'semua',
            selectedAlatId: defaultAlatId,
            selectedAlat: initialSelectedAlat,
            
            searchUser: initialSelectedUser ? initialSelectedUser.label : '',
            selectedUserId: defaultUserId,
            selectedUser: initialSelectedUser,
            showUserDropdown: false,
            
            tanggalMulai: defaultTanggalMulai,

            get filteredAlatList() {
                return this.alatList.filter(item => {
                    const matchKw = this.searchKeyword === '' || item.searchStr.includes(this.searchKeyword.toLowerCase());
                    const matchKat = this.kategoriFilter === 'semua' || item.kategori === this.kategoriFilter;
                    return matchKw && matchKat;
                });
            },

            get filteredUserList() {
                if (!this.searchUser || this.searchUser.trim() === '') return [];
                const exact = this.penggunaList.find(u => u.label === this.searchUser);
                if (exact && exact.id == this.selectedUserId) return [];
                const kw = this.searchUser.toLowerCase();
                return this.penggunaList.filter(u => u.keyword.includes(kw)).slice(0, 8);
            },

            selectTool(item) {
                if (item.isBorrowed || item.status !== 'tersedia') return;
                this.selectedAlatId = item.id;
                this.selectedAlat = item;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            selectUser(user) {
                this.selectedUserId = user.id;
                this.selectedUser = user;
                this.searchUser = user.label;
                this.showUserDropdown = false;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            init() {
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endpush
