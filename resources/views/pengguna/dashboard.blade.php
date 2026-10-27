@extends('layouts.app')

@section('title', 'Dashboard Pengguna')
@section('header', 'Beranda Anda')
@section('description', 'Ringkasan aktivitas peminjaman fasilitas Anda.')

@section('content')
<div class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card class="bg-gradient-to-br from-marun-800 to-marun-900 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-marun-100 text-sm font-medium">Total Peminjaman</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $statistik['total'] }}</h3>
                </div>
                <div class="p-3 bg-white/10 rounded-lg">
                    <i data-lucide="folder-open" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Menunggu Disetujui</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $statistik['menunggu'] }}</h3>
                </div>
                <div class="p-3 bg-mustard-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-mustard-600"></i>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Sedang Berlangsung</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $statistik['disetujui'] }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="play-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Telah Selesai</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $statistik['selesai'] }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="check-square" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Active Bookings -->
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-bold text-gray-900">Peminjaman Aktif</h3>
        </x-slot>

        @if($peminjamanAktif->isEmpty())
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="calendar" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h4 class="text-gray-900 font-medium">Tidak ada peminjaman aktif</h4>
                <p class="text-gray-500 text-sm mt-1 mb-4">Anda belum memiliki peminjaman yang sedang berlangsung atau menunggu persetujuan.</p>
                <a href="{{ route('peminjaman.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-marun-800 text-white text-sm font-medium rounded-lg hover:bg-marun-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Peminjaman
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3">Fasilitas</th>
                            <th scope="col" class="px-6 py-3">Jadwal</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peminjamanAktif as $p)
                            <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $p->fasilitas->nama_fasilitas }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $p->tanggal_mulai->format('d/m/Y H:i') }} - <br>
                                    {{ $p->tanggal_selesai->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :status="$p->status_pengajuan" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('peminjaman.show', $p->id) }}" class="text-marun-700 hover:text-marun-900 font-medium">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <!-- Recent History -->
    <x-card>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Terakhir</h3>
                <a href="{{ route('peminjaman.index') }}" class="text-sm font-medium text-marun-700 hover:text-marun-900">Lihat Semua</a>
            </div>
        </x-slot>

        @if($riwayatPeminjaman->isEmpty())
            <div class="text-center py-6 text-gray-500 text-sm">Belum ada riwayat peminjaman.</div>
        @else
            <div class="space-y-4">
                @foreach($riwayatPeminjaman->take(5) as $r)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-white rounded-md shadow-sm border border-gray-100 text-gray-400">
                                <i data-lucide="{{ $r->fasilitas->kategori->jenis === 'ruangan' ? 'door-open' : 'monitor' }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $r->fasilitas->nama_fasilitas }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $r->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <x-badge :status="$r->status_pengajuan" />
                            @if($r->status_pengajuan === 'selesai' && !$r->ulasan)
                                <div class="mt-2">
                                    <a href="{{ route('ulasan.buat', $r->id) }}" class="text-xs font-medium text-mustard-600 hover:text-mustard-700 underline">Beri Ulasan</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
@endsection
