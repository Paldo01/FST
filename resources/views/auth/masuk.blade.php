@extends('layouts.guest')

@section('title', 'Masuk - SIMORA FST')

@section('content')
<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden">
    <!-- Dekorasi -->
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-marun-800 to-mustard-500"></div>
    
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-marun-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-marun-100">
            <i data-lucide="log-in" class="w-8 h-8 text-marun-800"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Selamat Datang Kembali</h1>
        <p class="text-sm text-gray-500 mt-2">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email UINIB</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm transition-colors @error('email') border-red-500 @enderror"
                    placeholder="nama@fst-uinib.ac.id">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="kata_sandi" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                <a href="#" class="text-xs font-medium text-marun-700 hover:text-marun-800">Lupa sandi?</a>
            </div>
            <div class="relative" x-data="{ show: false }">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input :type="show ? 'text' : 'password'" name="kata_sandi" id="kata_sandi" required
                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm transition-colors"
                    placeholder="••••••••">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i data-lucide="eye" x-show="!show" class="w-4 h-4"></i>
                    <i data-lucide="eye-off" x-show="show" class="w-4 h-4" style="display: none;"></i>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="ingat_saya" name="ingat_saya" type="checkbox" class="h-4 w-4 text-marun-800 focus:ring-mustard-400 border-gray-300 rounded">
                <label for="ingat_saya" class="ml-2 block text-sm text-gray-700">
                    Ingat saya
                </label>
            </div>
            <a href="{{ route('lupa_sandi') }}" class="text-sm font-medium text-marun-700 hover:text-mustard-600 transition-colors">Lupa Kata Sandi?</a>
        </div>

        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-marun-800 hover:bg-marun-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mustard-400 transition-colors">
            Masuk
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm text-gray-600">
            Belum punya akun? 
            <a href="{{ route('daftar') }}" class="font-medium text-marun-700 hover:text-mustard-600 transition-colors">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection
