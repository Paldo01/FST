@extends('layouts.app')

@section('title', 'Input Jadwal Ruangan')
@section('header', 'Input Jadwal Ruangan')
@section('description', 'Masukkan jadwal penggunaan ruangan yang sudah disetujui secara langsung (misal: jadwal perkuliahan atau rapat rutin).')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fasilitas (Hanya Ruangan) -->
                <div>
                    <label for="fasilitas_id" class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                    <div class="relative">
                        <select name="fasilitas_id" id="fasilitas_id" required class="w-full rounded-lg border-gray-300 focus:border-marun-500 focus:ring-marun-500 pl-10">
                            <option value="">Pilih Ruangan</option>
                            @foreach($fasilitas as $f)
                                <option value="{{ $f->id }}" {{ old('fasilitas_id') == $f->id ? 'selected' : '' }}>
                                    {{ $f->nama_fasilitas }} ({{ $f->kategori->nama_kategori }})
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="building-2" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    </div>
                    @error('fasilitas_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pengguna -->
                <div>
                    <label for="pengguna_id" class="block text-sm font-medium text-gray-700 mb-1">Pengguna / Dosen Pengajar</label>
                    <div class="relative">
                        <select name="pengguna_id" id="pengguna_id" required class="w-full rounded-lg border-gray-300 focus:border-marun-500 focus:ring-marun-500 pl-10">
                            <option value="">Pilih Pengguna</option>
                            @foreach($pengguna as $p)
                                <option value="{{ $p->id }}" {{ old('pengguna_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_lengkap }} ({{ $p->nomor_identitas }})
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="user" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    </div>
                    @error('pengguna_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Waktu Mulai & Selesai -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                    <div class="relative">
                        <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" required value="{{ old('tanggal_mulai') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-marun-500 focus:ring-marun-500 pl-10">
                        <i data-lucide="calendar-clock" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    </div>
                    @error('tanggal_mulai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                    <div class="relative">
                        <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" required value="{{ old('tanggal_selesai') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-marun-500 focus:ring-marun-500 pl-10">
                        <i data-lucide="calendar-clock" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    </div>
                    @error('tanggal_selesai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tujuan Penggunaan -->
            <div>
                <label for="tujuan_peminjaman" class="block text-sm font-medium text-gray-700 mb-1">Mata Kuliah / Tujuan Penggunaan</label>
                <div class="relative">
                    <textarea name="tujuan_peminjaman" id="tujuan_peminjaman" rows="3" required
                        class="w-full rounded-lg border-gray-300 focus:border-marun-500 focus:ring-marun-500 pl-10 pt-2.5">{{ old('tujuan_peminjaman') }}</textarea>
                    <i data-lucide="file-text" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                </div>
                @error('tujuan_peminjaman')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('admin.verifikasi.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-marun-800 text-white rounded-lg text-sm font-medium hover:bg-marun-900 transition-colors flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
