@extends('layouts.app')

@section('title', 'Pengaturan Website - Super Admin')
@section('header', 'Pengaturan Website')
@section('description', 'Kelola identitas, logo, warna, dan informasi umum sistem.')

@section('content')
<div class="space-y-6 pb-12 max-w-5xl" 
     x-data="{
        nama: '{{ old('nama_website', $settings['nama_website']) }}',
        subNama: '{{ old('sub_nama_website', $settings['sub_nama_website']) }}',
        warna: '{{ old('warna_nama_website', $settings['warna_nama_website']) }}',
        previewLogo: '{{ $settings['logo_url'] }}',
        previewFavicon: '{{ $settings['favicon_url'] }}',
        updateLogoPreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewLogo = URL.createObjectURL(file);
            }
        },
        updateFaviconPreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewFavicon = URL.createObjectURL(file);
            }
        }
     }">

    <!-- Alert Sukses Flash Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="font-bold text-sm">Berhasil Disimpan!</p>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- Live Preview Banner Interaktif -->
    <div class="bg-linear-to-r from-gray-900 via-gray-800 to-gray-900 rounded-3xl p-6 text-white shadow-md relative overflow-hidden border border-gray-700/50">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-marun-600/20 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/10 text-white/90 mb-2">
                    <i data-lucide="eye" class="w-3.5 h-3.5 text-mustard-400"></i> Pratinjau Tampilan Identitas (Live Preview)
                </span>
                <p class="text-xs text-gray-300">Berikut simulasi bagaimana logo dan nama website tampil di header serta sidebar sistem:</p>
            </div>
            
            <!-- Simulasi Branding Sidebar Header -->
            <div class="bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3.5 min-w-[240px]">
                <img :src="previewLogo" 
                     alt="Preview Logo" 
                     class="w-10 h-10 object-contain drop-shadow-2xs select-none">
                <div class="flex flex-col text-left">
                    <span class="text-[11px] font-semibold text-gray-500 leading-tight" x-text="subNama || 'Sistem'"></span>
                    <span class="text-sm font-black leading-tight tracking-tight transition-colors"
                          :style="'color: ' + (warna || '#801414')"
                          x-text="nama || 'SIMORA FST'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Pengaturan -->
    <form action="{{ route('super_admin.pengaturan.simpan') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-6">
        @csrf

        <!-- 1. Identitas & Branding Website -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs p-6 sm:p-7 space-y-6">
            <div class="border-b border-gray-100 pb-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-marun-50 text-marun-800 flex items-center justify-center font-bold">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Identitas & Branding Utama</h2>
                    <p class="text-xs text-gray-500">Sesuaikan nama brand, sub-nama instansi, serta warna aksen teks website.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Nama Website -->
                <div class="md:col-span-2">
                    <label for="nama_website" class="block text-xs sm:text-sm font-bold text-gray-800 mb-1.5">
                        Nama Website <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_website" 
                           name="nama_website" 
                           x-model="nama"
                           required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold focus:ring-2 focus:ring-marun-500/20 focus:border-marun-600 transition-all placeholder:text-gray-400"
                           placeholder="Contoh: SIMORA FST atau Sistem Peminjaman FST">
                    <p class="text-[11px] text-gray-400 mt-1">Nama utama yang muncul di tab browser, sidebar, dan halaman muka.</p>
                    @error('nama_website')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warna Teks Brand -->
                <div>
                    <label for="warna_nama_website" class="block text-xs sm:text-sm font-bold text-gray-800 mb-1.5">
                        Warna Brand Teks
                    </label>
                    <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-xl border border-gray-200">
                        <input type="color" 
                               id="warna_nama_website" 
                               name="warna_nama_website" 
                               x-model="warna"
                               class="w-10 h-10 rounded-lg cursor-pointer border-0 bg-transparent p-0">
                        <div class="flex-1">
                            <span class="text-xs font-mono font-bold text-gray-700 uppercase" x-text="warna"></span>
                            <span class="block text-[10px] text-gray-400">Default: #801414</span>
                        </div>
                    </div>
                    @error('warna_nama_website')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub-nama / Tagline Instansi -->
                <div class="md:col-span-3">
                    <label for="sub_nama_website" class="block text-xs sm:text-sm font-bold text-gray-800 mb-1.5">
                        Tagline / Sub-nama Instansi
                    </label>
                    <input type="text" 
                           id="sub_nama_website" 
                           name="sub_nama_website" 
                           x-model="subNama"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium focus:ring-2 focus:ring-marun-500/20 focus:border-marun-600 transition-all placeholder:text-gray-400"
                           placeholder="Contoh: Sistem / Fakultas Sains dan Teknologi">
                    <p class="text-[11px] text-gray-400 mt-1">Label kecil di atas nama brand pada sidebar & navbar publik.</p>
                    @error('sub_nama_website')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 2. Logo & Favicon Website -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs p-6 sm:p-7 space-y-6">
            <div class="border-b border-gray-100 pb-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                    <i data-lucide="image" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Logo & Favicon Website</h2>
                    <p class="text-xs text-gray-500">Upload berkas logo instansi dan favicon tab browser (PNG, JPG, SVG, WebP).</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Upload Logo -->
                <div class="p-5 bg-gray-50/70 border border-gray-100 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs sm:text-sm font-bold text-gray-800">
                            Logo Website
                        </label>
                        <span class="text-[11px] text-gray-400">Maks. 2MB</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-white border border-gray-200 p-2 flex items-center justify-center shrink-0 shadow-2xs">
                            <img :src="previewLogo" 
                                 alt="Logo Website" 
                                 class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="file" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                   @change="updateLogoPreview($event)"
                                   class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#801414] file:text-white hover:file:bg-[#661010] cursor-pointer">
                            <p class="text-[11px] text-gray-400 mt-1.5">Disarankan gambar berlatar transparan (PNG/SVG).</p>
                        </div>
                    </div>
                    @error('logo')
                        <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Favicon -->
                <div class="p-5 bg-gray-50/70 border border-gray-100 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs sm:text-sm font-bold text-gray-800">
                            Favicon (Icon Browser)
                        </label>
                        <span class="text-[11px] text-gray-400">Maks. 1MB</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-white border border-gray-200 p-3 flex items-center justify-center shrink-0 shadow-2xs">
                            <img :src="previewFavicon" 
                                 alt="Favicon" 
                                 class="w-10 h-10 object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="file" 
                                   id="favicon" 
                                   name="favicon" 
                                   accept="image/png,image/jpeg,image/svg+xml,image/x-icon,image/webp"
                                   @change="updateFaviconPreview($event)"
                                   class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-800 file:text-white hover:file:bg-black cursor-pointer">
                            <p class="text-[11px] text-gray-400 mt-1.5">Muncul di tab browser (Icon persegi 32x32 atau 64x64).</p>
                        </div>
                    </div>
                    @error('favicon')
                        <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 3. Informasi Instansi & Kontak Layanan -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs p-6 sm:p-7 space-y-6">
            <div class="border-b border-gray-100 pb-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-800 flex items-center justify-center font-bold">
                    <i data-lucide="phone-call" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Informasi Instansi & Kontak Bantuan</h2>
                    <p class="text-xs text-gray-500">Informasi ini ditampilkan di halaman publik, email, serta kontak helpdesk peminjaman.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Kontak Email -->
                <div>
                    <label for="kontak_email" class="block text-xs sm:text-sm font-bold text-gray-800 mb-1.5">
                        Email Resmi Layanan
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" 
                               id="kontak_email" 
                               name="kontak_email" 
                               value="{{ old('kontak_email', $settings['kontak_email']) }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium focus:ring-2 focus:ring-marun-500/20 focus:border-marun-600 transition-all placeholder:text-gray-400"
                               placeholder="fst@uinib.ac.id">
                    </div>
                </div>

                <!-- Kontak Telepon / WhatsApp -->
                <div>
                    <label for="kontak_telepon" class="block text-xs sm:text-sm font-bold text-gray-800 mb-1.5">
                        No. Telepon / WhatsApp Helpdesk
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                        </div>
                        <input type="text" 
                               id="kontak_telepon" 
                               name="kontak_telepon" 
                               value="{{ old('kontak_telepon', $settings['kontak_telepon']) }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium focus:ring-2 focus:ring-marun-500/20 focus:border-marun-600 transition-all placeholder:text-gray-400"
                               placeholder="0751-123456 / 0812xxxxxx">
                    </div>
                </div>

                <!-- Alamat Instansi -->
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-xs sm:text-sm font-bold text-gray-800 mb-1.5">
                        Alamat Kampus / Fakultas
                    </label>
                    <input type="text" 
                           id="alamat" 
                           name="alamat" 
                           value="{{ old('alamat', $settings['alamat']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium focus:ring-2 focus:ring-marun-500/20 focus:border-marun-600 transition-all placeholder:text-gray-400"
                           placeholder="Kampus III UIN Imam Bonjol Padang, Sungai Bangek, Kota Padang">
                </div>

                <!-- Deskripsi Sistem -->
                <div class="md:col-span-2">
                    <label for="deskripsi_website" class="block text-xs sm:text-sm font-bold text-gray-800 mb-1.5">
                        Deskripsi Singkat Sistem
                    </label>
                    <textarea id="deskripsi_website" 
                              name="deskripsi_website" 
                              rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium focus:ring-2 focus:ring-marun-500/20 focus:border-marun-600 transition-all placeholder:text-gray-400"
                              placeholder="Deskripsi singkat mengenai sistem peminjaman fasilitas...">{{ old('deskripsi_website', $settings['deskripsi_website']) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Simpan -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('dashboard') }}" 
               class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold text-gray-600 hover:bg-gray-100 border border-gray-200 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-7 py-3 rounded-2xl text-xs sm:text-sm font-bold text-white bg-[#801414] hover:bg-[#661010] shadow-sm hover:shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Perubahan & Sinkronkan</span>
            </button>
        </div>
    </form>
</div>
@endsection
