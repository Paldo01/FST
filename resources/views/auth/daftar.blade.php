@extends('layouts.guest')

@section('title', 'Daftar - SIMORA FST')

@section('content')
<div class="w-full max-w-lg p-8 bg-white rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden my-8">
    <!-- Dekorasi -->
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-marun-800 to-mustard-500"></div>
    
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-marun-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-marun-100">
            <i data-lucide="user-plus" class="w-8 h-8 text-marun-800"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Buat Akun Baru</h1>
        <p class="text-sm text-gray-500 mt-2">Daftar untuk mulai meminjam fasilitas FST</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        
        <div>
            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required autofocus
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                    placeholder="Contoh: Budi Santoso">
            </div>
        </div>

        {{-- Pilih Jenis Pengguna --}}
        <div x-data="{ jenis: '{{ old('jenis_pengguna', 'mahasiswa') }}' }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Saya adalah</label>
            <div class="grid grid-cols-2 gap-3">
                <label :class="jenis === 'mahasiswa' ? 'border-marun-700 bg-marun-50 ring-2 ring-marun-300' : 'border-gray-200 bg-white hover:bg-gray-50'"
                    class="flex items-center gap-3 px-4 py-3 border-2 rounded-xl cursor-pointer transition-all">
                    <input type="radio" name="jenis_pengguna" value="mahasiswa" x-model="jenis" class="hidden">
                    <div :class="jenis === 'mahasiswa' ? 'bg-marun-700' : 'bg-gray-200'"
                        class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 transition-colors">
                        <div x-show="jenis === 'mahasiswa'" class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <div>
                        <p :class="jenis === 'mahasiswa' ? 'text-marun-800' : 'text-gray-700'" class="font-semibold text-sm">Mahasiswa</p>
                        <p class="text-xs text-gray-400">Gunakan NIM</p>
                    </div>
                </label>
                <label :class="jenis === 'dosen' ? 'border-marun-700 bg-marun-50 ring-2 ring-marun-300' : 'border-gray-200 bg-white hover:bg-gray-50'"
                    class="flex items-center gap-3 px-4 py-3 border-2 rounded-xl cursor-pointer transition-all">
                    <input type="radio" name="jenis_pengguna" value="dosen" x-model="jenis" class="hidden">
                    <div :class="jenis === 'dosen' ? 'bg-marun-700' : 'bg-gray-200'"
                        class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 transition-colors">
                        <div x-show="jenis === 'dosen'" class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <div>
                        <p :class="jenis === 'dosen' ? 'text-marun-800' : 'text-gray-700'" class="font-semibold text-sm">Dosen</p>
                        <p class="text-xs text-gray-400">Gunakan NIP</p>
                    </div>
                </label>
            </div>
            @error('jenis_pengguna')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="nomor_identitas" class="block text-sm font-medium text-gray-700 mb-1">NIM / NIP</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="credit-card" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="text" name="nomor_identitas" id="nomor_identitas" value="{{ old('nomor_identitas') }}" required
                        class="block w-full pl-10 pr-3 py-2 border @error('nomor_identitas') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                        placeholder="NIM / NIP">
                </div>
                @error('nomor_identitas')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="nomor_telepon" class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon') }}" required
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                        placeholder="08xxxxxxxx">
                </div>
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="block w-full pl-10 pr-3 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                    placeholder="nama@fst-uinib.ac.id">
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="kata_sandi" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="kata_sandi" id="kata_sandi" required minlength="8"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                        placeholder="Minimal 8 karakter">
                </div>
            </div>
            
            <div>
                <label for="kata_sandi_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Ulangi Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="kata_sandi_confirmation" id="kata_sandi_confirmation" required minlength="8"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                        placeholder="Ulangi sandi">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-marun-800 hover:bg-marun-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mustard-400 transition-colors mt-6">
            Daftar Akun
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('masuk') }}" class="font-medium text-marun-700 hover:text-mustard-600 transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
