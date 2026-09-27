@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi - SIMORA FST')

@section('content')
<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden my-8">
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-marun-800 to-mustard-500"></div>
    
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-marun-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-marun-100">
            <i data-lucide="key-round" class="w-8 h-8 text-marun-800"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Lupa Kata Sandi?</h1>
        <p class="text-sm text-gray-500 mt-2">Masukkan email Anda yang terdaftar. Kami akan mengirimkan 6 digit kode OTP.</p>
    </div>

    @if(session('pesan_error'))
    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"></i>
        <p class="text-sm text-red-800">{{ session('pesan_error') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('lupa_sandi.kirim') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Terdaftar</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="email" name="email" id="email" required autofocus
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                    placeholder="nama@fst-uinib.ac.id">
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-marun-800 hover:bg-marun-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mustard-400 transition-colors">
            Kirim Kode OTP
        </button>
    </form>

    <div class="mt-8 text-center">
        <a href="{{ route('masuk') }}" class="text-sm font-medium text-gray-600 hover:text-marun-700 transition-colors flex items-center justify-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Halaman Masuk
        </a>
    </div>
</div>
@endsection
