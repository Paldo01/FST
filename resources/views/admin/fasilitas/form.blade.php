@extends('layouts.app')

@php
    $isEdit = isset($fasilitas);
    $action = $isEdit ? route('admin.fasilitas.update', $fasilitas->id) : route('admin.fasilitas.store');
@endphp

@section('title', $isEdit ? 'Edit Fasilitas' : 'Tambah Fasilitas')
@section('header', $isEdit ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru')
@section('description', 'Lengkapi informasi fasilitas pada form di bawah ini.')

@section('header_actions')
    <a href="{{ route('admin.fasilitas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
@endsection

@section('content')
<div class="max-w-4xl">
    <x-card>
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div class="space-y-5">
                    <div>
                        <label for="kode_fasilitas" class="block text-sm font-medium text-gray-700 mb-1">Kode Fasilitas <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_fasilitas" id="kode_fasilitas" required 
                            value="{{ old('kode_fasilitas', $fasilitas->kode_fasilitas ?? '') }}"
                            class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                            placeholder="Contoh: R-101 atau LT-01">
                        @error('kode_fasilitas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nama_fasilitas" class="block text-sm font-medium text-gray-700 mb-1">Nama Fasilitas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_fasilitas" id="nama_fasilitas" required 
                            value="{{ old('nama_fasilitas', $fasilitas->nama_fasilitas ?? '') }}"
                            class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                            placeholder="Nama ruangan atau peralatan">
                        @error('nama_fasilitas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori_id" id="kategori_id" required 
                            class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id', $fasilitas->kategori_id ?? '') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }} ({{ ucfirst($k->jenis) }})
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm">
                                <option value="tersedia" {{ old('status', $fasilitas->status ?? '') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="dipinjam" {{ old('status', $fasilitas->status ?? '') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="perbaikan" {{ old('status', $fasilitas->status ?? '') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            </select>
                        </div>
                        <div>
                            <label for="kondisi_awal" class="block text-sm font-medium text-gray-700 mb-1">Kondisi Fisik</label>
                            <input type="text" name="kondisi_awal" id="kondisi_awal" required 
                                value="{{ old('kondisi_awal', $fasilitas->kondisi_awal ?? 'baik') }}"
                                class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                                placeholder="Baik/Baru">
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi & Spesifikasi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" 
                            class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm">{{ old('deskripsi', $fasilitas->deskripsi ?? '') }}</textarea>
                    </div>

                    <div x-data="{ 
                        photoName: null, 
                        photoPreview: null,
                        updatePreview(event) {
                            const file = event.target.files[0];
                            if(!file) return;
                            this.photoName = file.name;
                            const reader = new FileReader();
                            reader.onload = (e) => this.photoPreview = e.target.result;
                            reader.readAsDataURL(file);
                        }
                    }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Fasilitas (Opsional)</label>
                        
                        <div class="mt-1 flex items-center gap-4">
                            <div class="shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="h-20 w-20 object-cover rounded-lg border border-gray-200">
                                </template>
                                <template x-if="!photoPreview">
                                    @if($isEdit && $fasilitas->gambar)
                                        <img src="{{ Storage::url($fasilitas->gambar) }}" class="h-20 w-20 object-cover rounded-lg border border-gray-200">
                                    @else
                                        <div class="h-20 w-20 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center text-gray-400">
                                            <i data-lucide="image" class="w-8 h-8"></i>
                                        </div>
                                    @endif
                                </template>
                            </div>
                            <div>
                                <input type="file" name="gambar" id="gambar" class="hidden" accept="image/*" @change="updatePreview">
                                <label for="gambar" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors shadow-sm">
                                    <i data-lucide="upload" class="w-4 h-4 mr-2"></i> Pilih Gambar
                                </label>
                                <p class="text-xs text-gray-500 mt-2" x-text="photoName || 'Format: JPG, PNG (Max 2MB)'"></p>
                            </div>
                        </div>
                        @error('gambar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-marun-800 text-white font-medium text-sm rounded-lg hover:bg-marun-700 transition-colors shadow-sm">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Fasilitas' }}
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
