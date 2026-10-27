@extends('layouts.app')

@section('title', 'Detail Peminjaman #' . $peminjaman->id)
@section('header', 'Detail Peminjaman')
@section('description', 'Informasi lengkap mengenai status pengajuan fasilitas.')

@section('header_actions')
    <a href="{{ route('peminjaman.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center w-full">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Kegiatan</h3>
                    <x-badge :status="$peminjaman->status_pengajuan" />
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-4">
                <div class="col-span-2">
                    <p class="text-sm text-gray-500 mb-1">Tujuan / Nama Kegiatan</p>
                    <p class="font-medium text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-100">{{ $peminjaman->tujuan_peminjaman }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 mb-1">Tanggal Mulai</p>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-marun-700"></i>
                        <p class="font-medium text-gray-900">{{ $peminjaman->tanggal_mulai->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 mb-1">Tanggal Selesai</p>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-marun-700"></i>
                        <p class="font-medium text-gray-900">{{ $peminjaman->tanggal_selesai->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>

                @if($peminjaman->berkas_persyaratan)
                    <div class="col-span-2 border-t border-gray-100 pt-4 mt-2">
                        <p class="text-sm text-gray-500 mb-2">Berkas Pendukung</p>
                        <a href="{{ Storage::url($peminjaman->berkas_persyaratan) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                            <i data-lucide="file-text" class="w-4 h-4"></i> Lihat Dokumen PDF
                        </a>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Status Rejection or Return info -->
        @if($peminjaman->status_pengajuan === 'ditolak')
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mt-0.5 shrink-0"></i>
                    <div>
                        <h4 class="text-red-800 font-bold mb-1">Pengajuan Ditolak</h4>
                        <p class="text-red-700 text-sm">{{ $peminjaman->alasan_penolakan }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($peminjaman->pengembalian)
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-gray-900">Detail Pengembalian</h3>
                </x-slot>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500">Kondisi Alat/Ruang</p>
                            <div class="mt-1"><x-badge :status="$peminjaman->pengembalian->kondisi_akhir" /></div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Waktu Dikembalikan</p>
                            <p class="font-medium text-gray-900">{{ $peminjaman->pengembalian->tanggal_pengembalian->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Catatan Admin</p>
                        <p class="text-gray-900">{{ $peminjaman->pengembalian->catatan_admin ?: 'Tidak ada catatan.' }}</p>
                    </div>
                    
                    <div class="text-xs text-gray-400 mt-2">
                        Diverifikasi oleh: {{ $peminjaman->pengembalian->admin->nama_lengkap }}
                    </div>
                </div>
            </x-card>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900">Fasilitas</h3>
            </x-slot>
            
            <div class="text-center mb-4">
                @if($peminjaman->fasilitas->gambar)
                    <img src="{{ Storage::url($peminjaman->fasilitas->gambar) }}" alt="{{ $peminjaman->fasilitas->nama_fasilitas }}" class="w-full h-40 object-cover rounded-lg shadow-sm border border-gray-100">
                @else
                    <div class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                        <i data-lucide="image" class="w-12 h-12 opacity-50"></i>
                    </div>
                @endif
            </div>
            
            <h4 class="text-xl font-bold text-marun-900 text-center">{{ $peminjaman->fasilitas->nama_fasilitas }}</h4>
            <p class="text-center text-mustard-600 font-medium text-sm mt-1">{{ $peminjaman->fasilitas->kode_fasilitas }}</p>
            
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-sm">
                <span class="text-gray-500">Kategori</span>
                <span class="font-medium capitalize text-gray-900">{{ $peminjaman->fasilitas->kategori->nama_kategori }} ({{ $peminjaman->fasilitas->kategori->jenis }})</span>
            </div>
        </x-card>

        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-bold text-gray-900">Pemohon</h3>
                </x-slot>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Nama Lengkap</p>
                        <p class="font-medium text-gray-900">{{ $peminjaman->pengguna->nama_lengkap }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">NIM / NIP</p>
                        <p class="font-medium text-gray-900">{{ $peminjaman->pengguna->nomor_identitas }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Kontak</p>
                        <p class="font-medium text-gray-900">{{ $peminjaman->pengguna->nomor_telepon ?? '-' }}</p>
                    </div>
                </div>
            </x-card>
        @endif
    </div>
</div>
@endsection
