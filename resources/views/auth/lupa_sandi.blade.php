@extends('layouts.guest')

@section('title', 'Sistem Peminjaman FST - Lupa Kata Sandi')

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

<!-- Kartu Lupa Sandi Mengambang (Floating Card) -->
<div class="w-full max-w-[430px] bg-white rounded-3xl sm:rounded-[32px] shadow-2xl p-7 sm:p-9 border border-gray-100/80 relative z-30 transition-all">
    
    <!-- Header: Logo FST 3D Cube & Judul -->
    <div class="text-center mb-6">
        <div class="flex justify-center mb-3.5">
            <img src="{{ asset('images/logo-fst.png') }}" 
                 alt="Logo FST" 
                 class="w-14 sm:w-16 h-auto object-contain drop-shadow-xs select-none">
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#801414] tracking-tight leading-snug">
            Lupa Kata Sandi?
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Masukkan email Anda yang terdaftar untuk menerima 6 digit kode OTP.
        </p>
    </div>

    <!-- Alert / Pesan Error jika ada -->
    @if(session('pesan_error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-semibold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-red-600"></i>
            <span>{{ session('pesan_error') }}</span>
        </div>
    @endif

    <!-- Alert / Pesan Sukses jika ada -->
    @if(session('pesan_sukses'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-600"></i>
            <span>{{ session('pesan_sukses') }}</span>
        </div>
    @endif

    <!-- Form Lupa Sandi -->
    <form method="POST" action="{{ route('lupa_sandi.kirim') }}" class="space-y-4 sm:space-y-4.5">
        @csrf
        
        <!-- Field: Email Terdaftar -->
        <div>
            <label for="email" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">
                Email Terdaftar
            </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="block w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] text-xs sm:text-sm text-gray-800 text-center placeholder:text-center placeholder-gray-400 transition-all outline-none @error('email') border-red-500 @enderror"
                placeholder="nama@fst-uinib.ac.id">
            @error('email')
                <p class="text-red-600 text-[11px] sm:text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Kirim Kode OTP -->
        <button type="submit" 
                class="w-full mt-6 py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl text-sm sm:text-base font-bold text-white bg-[#801414] hover:bg-[#681010] active:scale-[0.99] transition-all shadow-md cursor-pointer flex items-center justify-center select-none">
            Kirim Kode OTP
        </button>
    </form>

    <!-- Link: Kembali ke Halaman Masuk -->
    <div class="mt-6 text-center">
        <a href="{{ route('masuk') }}" 
           class="text-xs sm:text-sm font-semibold text-[#801414] hover:underline transition-colors inline-flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Halaman Masuk
        </a>
    </div>

    <!-- Link: Belum punya akun? Daftar -->
    <div class="mt-3 text-center">
        <p class="text-xs sm:text-sm text-gray-600">
            Belum punya akun? 
            <a href="{{ route('daftar') }}" 
               class="font-bold text-[#801414] hover:underline transition-colors">
                Daftar
            </a>
        </p>
    </div>
</div>
@endsection
