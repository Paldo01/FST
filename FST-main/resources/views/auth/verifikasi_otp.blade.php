@extends('layouts.guest')

@section('title', 'Verifikasi OTP - SIMORA FST')

@section('content')
<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden my-8">
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-marun-800 to-mustard-500"></div>
    
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-mustard-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-mustard-100">
            <i data-lucide="shield-check" class="w-8 h-8 text-mustard-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Verifikasi Kode OTP</h1>
        <p class="text-sm text-gray-500 mt-2">Masukkan 6 digit kode OTP yang telah dikirimkan ke email <strong>{{ session('email_reset') }}</strong></p>
    </div>

    @if(session('pesan_sukses'))
    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 shrink-0 mt-0.5"></i>
        <p class="text-sm text-green-800">{{ session('pesan_sukses') }}</p>
    </div>
    @endif

    @if(session('pesan_error'))
    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"></i>
        <p class="text-sm text-red-800">{{ session('pesan_error') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('lupa_sandi.verifikasi.submit') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3 text-center">Kode OTP</label>
            <div class="flex gap-2 justify-center" x-data="otpInputs()">
                <template x-for="(i, index) in 6" :key="index">
                    <input type="text" maxlength="1" x-model="otp[index]" @input="handleInput($event, index)" @keydown.backspace="handleBackspace($event, index)"
                        class="w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-mustard-400 focus:border-mustard-400 outline-none transition-all">
                </template>
                <input type="hidden" name="otp" :value="otp.join('')">
            </div>
            @error('otp')
                <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-marun-800 hover:bg-marun-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mustard-400 transition-colors">
            Verifikasi OTP
        </button>
    </form>
</div>

<script>
    function otpInputs() {
        return {
            otp: ['', '', '', '', '', ''],
            handleInput(e, index) {
                if (e.target.value && index < 5) {
                    e.target.nextElementSibling.focus();
                }
            },
            handleBackspace(e, index) {
                if (!e.target.value && index > 0) {
                    e.target.previousElementSibling.focus();
                }
            }
        }
    }
</script>
@endsection
