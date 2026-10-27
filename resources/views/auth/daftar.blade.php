@extends('layouts.guest')

@section('title', 'Sistem Peminjaman FST - Daftar Akun')

@section('no_nav', true)
@section('no_footer', true)
@section('body_class', 'font-sans antialiased min-h-screen relative flex items-center justify-center overflow-x-hidden bg-slate-100')
@section('main_class', 'min-h-screen w-full flex items-center justify-center p-4 sm:p-6 relative z-20')

@section('content')
<!-- Sudut Kanan Atas: Pita Diagonal Marun -->
<svg class="pointer-events-none fixed top-0 right-0 w-32 h-32 sm:w-48 sm:h-48 z-10" viewBox="0 0 100 100" preserveAspectRatio="none">
    <polygon points="25,0 100,0 100,75" fill="#801414" />
</svg>

<!-- Sudut Kiri Bawah: Pita Diagonal Olive / Mustard -->
<svg class="pointer-events-none fixed bottom-0 left-0 w-32 h-32 sm:w-48 sm:h-48 z-10" viewBox="0 0 100 100" preserveAspectRatio="none">
    <polygon points="0,25 0,100 75,100" fill="#7e8537" />
</svg>

<!-- Kartu Pendaftaran Mengambang (Floating Card) -->
<div class="w-full max-w-[480px] bg-white rounded-3xl sm:rounded-[32px] shadow-2xl p-7 sm:p-9 border border-gray-100/80 relative z-30 transition-all my-6">
    
    <!-- Header: Logo FST 3D Cube & Judul -->
    <div class="text-center mb-6">
        <div class="flex justify-center mb-3.5">
            <img src="{{ asset('images/logo-fst.png') }}" 
                 alt="Logo FST" 
                 class="w-14 sm:w-16 h-auto object-contain drop-shadow-xs select-none">
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#801414] tracking-tight leading-snug">
            Daftar Akun
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Kelola peminjaman ruang dan alat
        </p>
    </div>

    <!-- Alert / Pesan Error Global jika ada -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-semibold space-y-1">
            <div class="flex items-center gap-1.5 font-bold">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-red-600"></i>
                <span>Terdapat kesalahan pada isian formulir:</span>
            </div>
            <ul class="list-disc list-inside pl-1 text-[11px] text-red-600 font-normal">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Pendaftaran -->
    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ jenis: '{{ old('jenis_pengguna', 'mahasiswa') }}', showPass: false, showConfirm: false }">
        @csrf
        
        <!-- Field 1: Nama Lengkap -->
        <div>
            <label for="nama_lengkap" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                Nama Lengkap
            </label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required autofocus
                class="block w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none @error('nama_lengkap') border-red-500 @enderror"
                placeholder="Masukkan nama lengkap">
            @error('nama_lengkap')
                <p class="text-red-600 text-[11px] sm:text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Field 2: Email & NIM / NIP (Grid 2 Kolom) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
                <label for="email" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                    Email
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="block w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none @error('email') border-red-500 @enderror"
                    placeholder="Masukkan email">
                @error('email')
                    <p class="text-red-600 text-[11px] sm:text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nomor_identitas" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                    <span x-text="jenis === 'dosen' ? 'NIP' : 'NIM'">NIM</span>
                </label>
                <input type="text" name="nomor_identitas" id="nomor_identitas" value="{{ old('nomor_identitas') }}" required
                    class="block w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none @error('nomor_identitas') border-red-500 @enderror"
                    :placeholder="jenis === 'dosen' ? 'Masukkan NIP' : 'Masukkan NIM'">
                @error('nomor_identitas')
                    <p class="text-red-600 text-[11px] sm:text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Field 3: No. WhatsApp (Opsional) -->
        <div>
            <label for="nomor_telepon" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                No. WhatsApp <span class="text-gray-400 font-normal text-xs">(Opsional)</span>
            </label>
            <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon') }}"
                class="block w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none @error('nomor_telepon') border-red-500 @enderror"
                placeholder="Masukkan no. WhatsApp">
            @error('nomor_telepon')
                <p class="text-red-600 text-[11px] sm:text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Field 4: Password & Konfirmasi Password (Grid 2 Kolom) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
                <label for="kata_sandi" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="kata_sandi" id="kata_sandi" required minlength="8"
                        class="block w-full pl-4 pr-10 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none @error('kata_sandi') border-red-500 @enderror"
                        placeholder="Masukkan password">
                    <button type="button" @click="showPass = !showPass" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                            tabindex="-1" title="Lihat password">
                        <i data-lucide="eye" x-show="!showPass" class="w-4 h-4"></i>
                        <i data-lucide="eye-off" x-show="showPass" class="w-4 h-4" style="display: none;"></i>
                    </button>
                </div>
                @error('kata_sandi')
                    <p class="text-red-600 text-[11px] sm:text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kata_sandi_confirmation" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                    Konfirmasi Password
                </label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="kata_sandi_confirmation" id="kata_sandi_confirmation" required minlength="8"
                        class="block w-full pl-4 pr-10 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none"
                        placeholder="Masukkan ulang password">
                    <button type="button" @click="showConfirm = !showConfirm" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                            tabindex="-1" title="Lihat konfirmasi password">
                        <i data-lucide="eye" x-show="!showConfirm" class="w-4 h-4"></i>
                        <i data-lucide="eye-off" x-show="showConfirm" class="w-4 h-4" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Field 5: Jenis Pengguna (Dosen / Mahasiswa Radio Sesuai Mockup) -->
        <div class="pt-2 text-center">
            <p class="font-bold text-gray-800 text-xs sm:text-sm mb-2">Jenis Pengguna</p>
            <div class="flex items-center justify-center gap-8 sm:gap-12">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none group">
                    <input type="radio" name="jenis_pengguna" value="dosen" x-model="jenis"
                           class="w-4 h-4 text-[#801414] border-gray-300 focus:ring-[#801414] focus:ring-1 cursor-pointer">
                    <span class="text-xs sm:text-sm font-bold text-gray-800 group-hover:text-[#801414] transition-colors">Dosen</span>
                </label>
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none group">
                    <input type="radio" name="jenis_pengguna" value="mahasiswa" x-model="jenis"
                           class="w-4 h-4 text-[#801414] border-gray-300 focus:ring-[#801414] focus:ring-1 cursor-pointer">
                    <span class="text-xs sm:text-sm font-bold text-gray-800 group-hover:text-[#801414] transition-colors">Mahasiswa</span>
                </label>
            </div>
            @error('jenis_pengguna')
                <p class="text-red-600 text-[11px] sm:text-xs mt-1.5 font-medium">{{ $message }}</p>
            @enderror
            <div x-show="jenis === 'dosen'" x-transition class="mt-2.5">
                <span class="inline-block text-[11px] text-amber-800 bg-amber-50 border border-amber-200/70 px-3 py-1 rounded-full font-medium">
                    *Akun Dosen akan diverifikasi oleh Admin sebelum dapat digunakan.
                </span>
            </div>
        </div>

        <!-- Tombol Daftar -->
        <button type="submit" 
                class="w-full mt-4 sm:mt-5 py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl text-sm sm:text-base font-bold text-white bg-[#801414] hover:bg-[#681010] active:scale-[0.99] transition-all shadow-md cursor-pointer flex items-center justify-center select-none">
            Daftar
        </button>
    </form>

    <!-- Link: Sudah punya akun? Masuk -->
    <div class="mt-5 text-center">
        <p class="text-xs sm:text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('masuk') }}" 
               class="font-bold text-[#801414] hover:underline transition-colors">
                Masuk
            </a>
        </p>
    </div>
</div>
@endsection
