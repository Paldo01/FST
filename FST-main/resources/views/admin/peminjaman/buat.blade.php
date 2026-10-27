@extends('layouts.app')

@section('title', 'Input Peminjaman')
@section('header', 'Input Peminjaman (Admin)')
@section('description', 'Catat peminjaman alat/barang secara langsung untuk dosen atau mahasiswa.')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.pengembalian.index') }}" class="text-marun-800 hover:text-marun-900 font-medium text-sm flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{
        tanggalMulai: ''
    }">
        @csrf
        
        <div class="h-2 bg-gradient-to-r from-marun-800 to-mustard-500"></div>
        
        <div class="p-6 sm:p-8 space-y-8">
            {{-- Bagian Peminjam --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-marun-800"></i>
                    Pilih Peminjam
                </h3>
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pengguna (Dosen/Mahasiswa)</label>
                    
                    @php
                        $oldPengguna = old('pengguna_id') ? $pengguna->firstWhere('id', old('pengguna_id')) : null;
                        $oldLabel = $oldPengguna ? addslashes($oldPengguna->nama_lengkap . ' (' . $oldPengguna->nomor_identitas . ') - ' . ucfirst($oldPengguna->jenis_pengguna)) : '';
                    @endphp

                    <div x-data="{
                        searchPengguna: '{{ $oldLabel }}',
                        showDropdown: false,
                        selectedId: '{{ old('pengguna_id') }}',
                        penggunaList: [
                            @foreach($pengguna as $p)
                            { id: {{ $p->id }}, label: '{{ addslashes($p->nama_lengkap) }} ({{ $p->nomor_identitas }}) - {{ ucfirst($p->jenis_pengguna) }}', keyword: '{{ addslashes(strtolower($p->nama_lengkap . ' ' . $p->nomor_identitas)) }}' },
                            @endforeach
                        ],
                        get filteredPengguna() {
                            if (this.searchPengguna === '') return [];
                            // If exactly matches the selected label, don't show the dropdown (it means they selected it)
                            const exactMatch = this.penggunaList.find(p => p.label === this.searchPengguna);
                            if (exactMatch && exactMatch.id == this.selectedId) return [];
                            
                            return this.penggunaList.filter(p => p.keyword.includes(this.searchPengguna.toLowerCase()));
                        },
                        selectUser(p) {
                            this.selectedId = p.id;
                            this.searchPengguna = p.label;
                            this.showDropdown = false;
                        }
                    }" class="relative">
                        
                        <!-- Input hidden to submit the selected ID -->
                        <input type="hidden" name="pengguna_id" x-model="selectedId" required>
                        
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchPengguna" 
                                @focus="showDropdown = true" 
                                @click.away="showDropdown = false"
                                @input="showDropdown = true; selectedId = ''"
                                class="pl-10 w-full rounded-lg text-sm bg-white p-2.5 transition-colors {{ $errors->has('pengguna_id') ? 'border-red-500' : 'border-gray-300 focus:ring-mustard-400 focus:border-mustard-400' }}"
                                placeholder="Ketik NIM / NIP atau Nama pengguna..." autocomplete="off">
                                
                            <!-- Clear button -->
                            <button type="button" x-show="searchPengguna !== ''" @click="searchPengguna = ''; selectedId = ''; showDropdown = true; $el.previousElementSibling.focus()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <!-- Autocomplete Results -->
                        <div x-show="showDropdown && searchPengguna !== '' && filteredPengguna.length > 0" x-transition style="display: none;" 
                             class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div class="p-1">
                                <template x-for="p in filteredPengguna" :key="p.id">
                                    <div @click="selectUser(p)" 
                                         class="px-4 py-3 text-sm text-gray-700 hover:bg-marun-50 hover:text-marun-900 cursor-pointer rounded-md border-b border-gray-50 last:border-0"
                                         x-text="p.label">
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <div x-show="showDropdown && searchPengguna !== '' && filteredPengguna.length === 0 && selectedId === ''" style="display: none;" 
                             class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                            <div class="px-4 py-3 text-sm text-center text-gray-500">
                                Pengguna tidak ditemukan
                            </div>
                        </div>
                    </div>

                    @error('pengguna_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian Fasilitas --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-marun-800"></i>
                    Pilih Fasilitas
                </h3>
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alat / Barang</label>
                    <select name="fasilitas_id" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-mustard-400 focus:border-mustard-400 @error('fasilitas_id') border-red-500 @enderror">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($fasilitas as $f)
                            <option value="{{ $f->id }}" {{ old('fasilitas_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->nama_fasilitas }} ({{ $f->kode_fasilitas }}) - Kategori: {{ $f->kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('fasilitas_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian Jadwal & Keterangan --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-marun-800"></i>
                    Waktu Peminjaman & Keterangan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-5 rounded-xl border border-gray-100">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Diambil (Tanggal & Jam)</label>
                        <input type="datetime-local" name="tanggal_mulai" x-model="tanggalMulai" required
                            class="w-full md:w-1/2 rounded-lg border-gray-300 text-sm focus:ring-mustard-400 focus:border-mustard-400 @error('tanggal_mulai') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Waktu pengembalian akan dikalkulasi otomatis saat Anda menyelesaikan peminjaman ini di halaman Pengembalian Alat.</p>
                        @error('tanggal_mulai')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Peminjaman</label>
                        <textarea name="tujuan_peminjaman" rows="3" required placeholder="Contoh: Praktikum Fisika Dasar, Peminjaman Proyektor untuk Rapat..."
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-mustard-400 focus:border-mustard-400 @error('tujuan_peminjaman') border-red-500 @enderror">{{ old('tujuan_peminjaman') }}</textarea>
                        @error('tujuan_peminjaman')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-gray-50 px-6 py-5 border-t border-gray-100 flex justify-end gap-3 items-center">
            <span class="text-sm text-gray-500 italic mr-4">Status akan langsung "Disetujui"</span>
            <button type="reset" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100">
                Reset
            </button>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-marun-800 rounded-lg hover:bg-marun-900 focus:ring-4 focus:outline-none focus:ring-marun-300 flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Peminjaman
            </button>
        </div>
    </form>
</div>
@endsection
