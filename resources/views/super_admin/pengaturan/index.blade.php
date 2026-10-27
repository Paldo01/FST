@extends('layouts.app')

@section('title', 'Pengaturan Website')
@section('header', 'Pengaturan Website')
@section('description', 'Kelola pengaturan identitas dan tampilan website.')

@section('content')
<div class="max-w-3xl space-y-6">
    <x-card>
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i data-lucide="settings-2" class="w-5 h-5 text-marun-800"></i>
            Identitas Website
        </h3>

        <form action="{{ route('super_admin.pengaturan.simpan') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-gray-50 p-5 rounded-xl border border-gray-100 mb-6">
                <div>
                    <label for="nama_website" class="block text-sm font-medium text-gray-700 mb-2">Nama Website</label>
                    <input type="text" name="nama_website" id="nama_website" 
                        value="{{ old('nama_website', $namaWebsite->value ?? 'SIMORA FST') }}"
                        class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                        placeholder="Contoh: SIMORA FST">
                    <p class="mt-1 text-xs text-gray-500">Nama ini akan muncul di pojok kiri atas (sebelah logo).</p>
                    @error('nama_website')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="warna_nama_website" class="block text-sm font-medium text-gray-700 mb-2">Warna Teks Nama Website</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="warna_nama_website" id="warna_nama_website" 
                            value="{{ old('warna_nama_website', $warnaNamaWebsite->value ?? '#ffffff') }}"
                            class="block w-12 h-10 rounded cursor-pointer border-0 p-0">
                        <span class="text-xs text-gray-500">Pilih warna untuk tulisan (Bawaan: Putih untuk sidebar, Merah marun untuk Beranda).</span>
                    </div>
                    @error('warna_nama_website')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-start gap-6 bg-gray-50 p-5 rounded-xl border border-gray-100">
                <div class="shrink-0">
                    <p class="text-sm font-medium text-gray-700 mb-2">Logo Saat Ini:</p>
                    <div class="w-32 h-32 bg-white border border-gray-200 rounded-lg flex items-center justify-center p-2">
                        @if($logo && $logo->value)
                            <img src="{{ Storage::url($logo->value) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        @else
                            <div class="text-center text-gray-400">
                                <i data-lucide="image-off" class="w-8 h-8 mx-auto mb-1"></i>
                                <span class="text-xs">Belum ada logo</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex-1 w-full">
                    <p class="text-sm font-medium text-gray-700 mb-2">Upload Logo Baru:</p>
                    <input type="file" name="logo" accept="image/png, image/jpeg, image/svg+xml"
                        class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-marun-50 file:text-marun-700
                        hover:file:bg-marun-100
                        border border-gray-300 rounded-lg cursor-pointer bg-white">
                    <p class="mt-2 text-xs text-gray-500">
                        Biarkan kosong jika tidak ingin mengubah logo. Format: PNG, JPG, SVG. Maks 2MB.
                    </p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 text-right">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-marun-800 text-white text-sm font-medium rounded-lg hover:bg-marun-900 transition-colors shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
