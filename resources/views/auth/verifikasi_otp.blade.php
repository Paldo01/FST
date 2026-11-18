@extends('layouts.guest')

@section('title', ($appNamaWebsite ?? 'Sistem Peminjaman FST') . ' - Verifikasi OTP')

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
    
    <!-- Header: Logo & Judul Dinamis -->
    <div class="text-center mb-6">
        <div class="flex justify-center mb-3.5">
            <img src="{{ $appLogo ?? asset('images/logo-fst.png') }}" 
                 alt="{{ $appNamaWebsite ?? 'Logo' }}" 
                 class="w-14 sm:w-16 h-auto max-h-16 object-contain drop-shadow-xs select-none">
        </div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight leading-snug"
            style="color: {{ $appWarnaNamaWebsite ?? '#801414' }};">
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
    <form method="POST" action="{{ route('lupa_sandi.verifikasi.submit') }}" class="space-y-6" id="otpForm">
        @csrf
        <div>
            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-3 text-center">
                Kode Verifikasi (6 Digit)
            </label>
            
            <div class="flex gap-2 sm:gap-2.5 justify-center" 
                 x-data="otpInputs()" 
                 x-init="initFocus()">
                <template x-for="(digit, index) in otp" :key="index">
                    <input type="text" 
                           maxlength="1" 
                           inputmode="numeric" 
                           pattern="[0-9]*"
                           :id="'otp-box-' + index"
                           x-model="otp[index]" 
                           @input="handleInput($event, index)" 
                           @keydown.backspace="handleBackspace($event, index)"
                           @keydown.arrow-left="focusPrev(index)"
                           @keydown.arrow-right="focusNext(index)"
                           @paste="handlePaste($event)"
                           autocomplete="off"
                           class="w-10 h-12 sm:w-12 sm:h-14 text-center text-lg sm:text-xl font-black text-[#801414] border border-gray-300 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414] outline-none transition-all shadow-xs bg-gray-50/50 focus:bg-white">
                </template>
                <input type="hidden" name="otp" :value="otp.join('')">
            </div>

            @error('otp')
                <p class="text-red-600 text-[11px] sm:text-xs mt-2 text-center font-medium">{{ $message }}</p>
            @enderror

            <p class="text-[11px] sm:text-xs text-gray-500 text-center mt-2.5 flex items-center justify-center gap-1">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i>
                <span>Kode OTP berlaku selama <strong>5 menit</strong>.</span>
            </p>
        </div>

        <!-- Tombol Verifikasi OTP -->
        <button type="submit" 
                class="w-full mt-6 py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl text-sm sm:text-base font-bold text-white bg-[#801414] hover:bg-[#681010] active:scale-[0.99] transition-all shadow-md cursor-pointer flex items-center justify-center gap-2 select-none">
            <i data-lucide="shield-check" class="w-5 h-5"></i>
            <span>Verifikasi OTP</span>
        </button>
    </form>

    <!-- Bagian Kirim Ulang OTP dengan Countdown Timer 5 Menit (300 Detik) -->
    @php
        $sentAt = session('otp_sent_at', now()->timestamp);
        $elapsed = max(0, now()->timestamp - $sentAt);
        $initialTimer = max(0, 300 - $elapsed);
    @endphp

    <div class="mt-6 text-center space-y-3 pt-4 border-t border-gray-100"
         x-data="{
             timer: {{ $initialTimer }},
             initTimer() {
                 if (this.timer > 0) {
                     let interval = setInterval(() => {
                         this.timer--;
                         if (this.timer <= 0) {
                             clearInterval(interval);
                         }
                     }, 1000);
                 }
             },
             get formattedTimer() {
                 let minutes = Math.floor(this.timer / 60);
                 let seconds = this.timer % 60;
                 return (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
             }
         }"
         x-init="initTimer()">
        
        <p class="text-xs sm:text-sm text-gray-600">
            Tidak menerima kode OTP?
        </p>

        <!-- Form Kirim Ulang OTP Langsung -->
        <form method="POST" action="{{ route('lupa_sandi.kirim_ulang') }}">
            @csrf
            <template x-if="timer > 0">
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-400 font-semibold cursor-not-allowed">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    Kirim ulang dalam <span class="font-mono text-[#801414]" x-text="formattedTimer"></span>
                </span>
            </template>
            <template x-if="timer <= 0">
                <button type="submit" 
                        class="text-xs sm:text-sm font-bold text-[#801414] hover:text-[#681010] hover:underline transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Kirim Ulang Kode OTP
                </button>
            </template>
        </form>

        <div class="pt-2">
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
            initFocus() {
                this.$nextTick(() => {
                    const firstInput = document.getElementById('otp-box-0');
                    if (firstInput) firstInput.focus();
                });
            },
            handleInput(e, index) {
                // Bersihkan karakter non-angka
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val;
                this.otp[index] = val;

                if (val && index < 5) {
                    this.focusNext(index);
                }

                // Cek jika seluruh 6 digit sudah terisi, auto submit
                if (this.otp.every(d => d !== '')) {
                    // Beri jeda 150ms agar input terupdate
                    setTimeout(() => {
                        document.getElementById('otpForm').submit();
                    }, 150);
                }
            },
            handleBackspace(e, index) {
                if (!this.otp[index] && index > 0) {
                    this.focusPrev(index);
                } else {
                    this.otp[index] = '';
                }
            },
            focusNext(index) {
                const next = document.getElementById('otp-box-' + (index + 1));
                if (next) next.focus();
            },
            focusPrev(index) {
                const prev = document.getElementById('otp-box-' + (index - 1));
                if (prev) prev.focus();
            },
            handlePaste(e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                const cleanDigits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);

                if (cleanDigits.length > 0) {
                    for (let i = 0; i < 6; i++) {
                        this.otp[i] = cleanDigits[i] || '';
                        const inputElem = document.getElementById('otp-box-' + i);
                        if (inputElem) inputElem.value = this.otp[i];
                    }

                    const lastIndex = Math.min(cleanDigits.length - 1, 5);
                    const lastInput = document.getElementById('otp-box-' + lastIndex);
                    if (lastInput) lastInput.focus();

                    // Jika tepat 6 digit, otomatis submit
                    if (cleanDigits.length === 6) {
                        setTimeout(() => {
                            document.getElementById('otpForm').submit();
                        }, 200);
                    }
                }
            }
        };
    }
</script>
@endsection
