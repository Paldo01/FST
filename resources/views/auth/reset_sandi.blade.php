@extends('layouts.guest')

@section('title', 'Buat Kata Sandi Baru - SIMORA FST')

@section('content')
<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden my-8">
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-marun-800 to-mustard-500"></div>
    
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-marun-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-marun-100">
            <i data-lucide="lock" class="w-8 h-8 text-marun-800"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Buat Kata Sandi Baru</h1>
        <p class="text-sm text-gray-500 mt-2">Silakan buat kata sandi baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('lupa_sandi.reset.submit') }}" class="space-y-5">
        @csrf
        <div>
            <label for="kata_sandi" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="password" name="kata_sandi" id="kata_sandi" required minlength="8" autofocus
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                    placeholder="Minimal 8 karakter">
            </div>
            @error('kata_sandi')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kata_sandi_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Ulangi Kata Sandi Baru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="password" name="kata_sandi_confirmation" id="kata_sandi_confirmation" required minlength="8"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                    placeholder="Ulangi kata sandi">
            </div>
        </div>

        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-marun-800 hover:bg-marun-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mustard-400 transition-colors mt-6">
            Simpan Kata Sandi
        </button>
    </form>
</div>
@endsection
