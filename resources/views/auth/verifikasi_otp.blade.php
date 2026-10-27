@extends('layouts.guest')

@section('title', 'Sistem Peminjaman FST - Verifikasi OTP')

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

<!-- Kartu Verifikasi OTP Mengambang (Floating Card) -->
<div class="w-full max-w-[440px] bg-white rounded-3xl sm:rounded-[32px] shadow-2xl p-7 sm:p-9 border border-gray-100/80 relative z-30 transition-all">
    
    <!-- Header: Logo FST 3D Cube & Judul -->
    <div class="text-center mb-6">
        <div class="flex justify-center mb-3.5">
            <img src="{{ asset('images/logo-fst.png') }}" 
                 alt="Logo FST" 
                 class="w-14 sm:w-16 h-auto object-contain drop-shadow-xs select-none">
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#801414] tracking-tight leading-snug">
            Verifikasi Kode OTP
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Masukkan 6 digit kode OTP yang telah dikirimkan ke email:
            <span class="block font-bold text-gray-800 mt-0.5 break-all">{{ session('email_reset') }}</span>
        </p>
    </div>

    <!-- Alert / Pesan Sukses -->
    @if(session('pesan_sukses'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-600"></i>
            <span>{{ session('pesan_sukses') }}</span>
        </div>
    @endif

    <!-- Alert / Pesan Error -->
    @if(session('pesan_error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-semibold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-red-600"></i>
            <span>{{ session('pesan_error') }}</span>
        </div>
    @endif

    <!-- Form Verifikasi OTP -->
    <form method="POST" action="{{ route('lupa_sandi.verifikasi.submit') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-3 text-center">
                Kode Verifikasi
            </label>
            <div class="flex gap-2 sm:gap-2.5 justify-center" x-data="otpInputs()">
                <template x-for="(i, index) in 6" :key="index">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                        x-model="otp[index]" 
                        @input="handleInput($event, index)" 
                        @keydown.backspace="handleBackspace($event, index)"
                        class="w-10 h-12 sm:w-12 sm:h-14 text-center text-lg sm:text-xl font-black text-[#801414] border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] outline-none transition-all shadow-xs">
                </template>
                <input type="hidden" name="otp" :value="otp.join('')">
            </div>
            @error('otp')
                <p class="text-red-600 text-[11px] sm:text-xs mt-2 text-center font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Verifikasi OTP -->
        <button type="submit" 
                class="w-full mt-6 py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl text-sm sm:text-base font-bold text-white bg-[#801414] hover:bg-[#681010] active:scale-[0.99] transition-all shadow-md cursor-pointer flex items-center justify-center select-none">
            Verifikasi OTP
        </button>
    </form>

    <!-- Navigasi Bawah -->
    <div class="mt-6 text-center space-y-2">
        <p class="text-xs sm:text-sm text-gray-600">
            Tidak menerima kode? 
            <a href="{{ route('lupa_sandi') }}" class="font-bold text-[#801414] hover:underline transition-colors">
                Kirim Ulang OTP
            </a>
        </p>
        <div>
            <a href="{{ route('masuk') }}" class="text-xs sm:text-sm text-gray-500 hover:text-gray-800 transition-colors inline-flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Halaman Masuk
            </a>
        </div>
    </div>
</div>

<script>
    function otpInputs() {
        return {
            otp: ['', '', '', '', '', ''],
            handleInput(e, index) {
                // Hanya izinkan angka
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                this.otp[index] = e.target.value;
                if (e.target.value && index < 5) {
                    const inputs = e.target.parentElement.querySelectorAll('input[type="text"]');
                    if (inputs[index + 1]) inputs[index + 1].focus();
                }
            },
            handleBackspace(e, index) {
                if (!e.target.value && index > 0) {
                    const inputs = e.target.parentElement.querySelectorAll('input[type="text"]');
                    if (inputs[index - 1]) inputs[index - 1].focus();
                }
            }
        }
    }
</script>
@endsection
