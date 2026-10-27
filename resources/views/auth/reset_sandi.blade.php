@extends('layouts.guest')

@section('title', 'Sistem Peminjaman FST - Buat Kata Sandi Baru')

@section('no_nav', true)
@section('no_footer', true)
@section('body_class', 'font-sans antialiased min-h-screen relative flex items-center justify-center overflow-x-hidden bg-slate-100')
@section('main_class', 'min-h-screen w-full flex items-center justify-center p-4 relative z-20')

@section('content')

<!-- Sudut Kanan Atas: Pita Diagonal Marun -->
<svg class="pointer-events-none fixed top-0 right-0 w-32 h-32 sm:w-48 sm:h-48 z-10" viewBox="0 0 100 100" preserveAspectRatio="none">
    <polygon points="25,0 100,0 100,75" fill="#801414" />
</svg>

<!-- Sudut Kiri Bawah: Pita Diagonal Olive / Mustard -->
<svg class="pointer-events-none fixed bottom-0 left-0 w-32 h-32 sm:w-48 sm:h-48 z-10" viewBox="0 0 100 100" preserveAspectRatio="none">
    <polygon points="0,25 0,100 75,100" fill="#7e8537" />
</svg>

<!-- Kartu Reset Sandi Mengambang (Floating Card) -->
<div class="w-full max-w-[430px] bg-white rounded-3xl sm:rounded-[32px] shadow-2xl p-7 sm:p-9 border border-gray-100/80 relative z-30 transition-all">
    
    <!-- Header: Logo FST 3D Cube & Judul -->
    <div class="text-center mb-6">
        <div class="flex justify-center mb-3.5">
            <img src="{{ asset('images/logo-fst.png') }}" 
                 alt="Logo FST" 
                 class="w-14 sm:w-16 h-auto object-contain drop-shadow-xs select-none">
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#801414] tracking-tight leading-snug">
            Buat Kata Sandi Baru
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Silakan buat kata sandi baru untuk akun Anda.
        </p>
    </div>

    <!-- Alert / Pesan Error jika ada -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-semibold space-y-1">
            <div class="flex items-center gap-1.5 font-bold">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-red-600"></i>
                <span>Terdapat kesalahan:</span>
            </div>
            <ul class="list-disc list-inside pl-1 text-[11px] text-red-600 font-normal">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Reset Sandi -->
    <form method="POST" action="{{ route('lupa_sandi.reset.submit') }}" class="space-y-4 sm:space-y-4.5" x-data="{ showPass: false, showConfirm: false }">
        @csrf
        
        <!-- Field 1: Kata Sandi Baru -->
        <div>
            <label for="kata_sandi" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                Kata Sandi Baru
            </label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" name="kata_sandi" id="kata_sandi" required minlength="8" autofocus
                    class="block w-full pl-4 pr-10 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none @error('kata_sandi') border-red-500 @enderror"
                    placeholder="Minimal 8 karakter">
                <button type="button" @click="showPass = !showPass" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                        tabindex="-1" title="Lihat password">
                    <i data-lucide="eye" x-show="!showPass" class="w-4 h-4"></i>
                    <i data-lucide="eye-off" x-show="showPass" class="w-4 h-4" style="display: none;"></i>
                </button>
            </div>
            @error('kata_sandi')
                <p class="text-red-600 text-[11px] sm:text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Field 2: Ulangi Kata Sandi Baru -->
        <div>
            <label for="kata_sandi_confirmation" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                Ulangi Kata Sandi Baru
            </label>
            <div class="relative">
                <input :type="showConfirm ? 'text' : 'password'" name="kata_sandi_confirmation" id="kata_sandi_confirmation" required minlength="8"
                    class="block w-full pl-4 pr-10 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none"
                    placeholder="Masukkan ulang kata sandi">
                <button type="button" @click="showConfirm = !showConfirm" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                        tabindex="-1" title="Lihat konfirmasi password">
                    <i data-lucide="eye" x-show="!showConfirm" class="w-4 h-4"></i>
                    <i data-lucide="eye-off" x-show="showConfirm" class="w-4 h-4" style="display: none;"></i>
                </button>
            </div>
        </div>

        <!-- Tombol Simpan Kata Sandi -->
        <button type="submit" 
                class="w-full mt-6 py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl text-sm sm:text-base font-bold text-white bg-[#801414] hover:bg-[#681010] active:scale-[0.99] transition-all shadow-md cursor-pointer flex items-center justify-center select-none">
            Simpan Kata Sandi
        </button>
    </form>

    <!-- Link: Kembali ke Halaman Masuk -->
    <div class="mt-6 text-center">
        <a href="{{ route('masuk') }}" 
           class="text-xs sm:text-sm font-semibold text-[#801414] hover:underline transition-colors inline-flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Halaman Masuk
        </a>
    </div>
</div>
@endsection
