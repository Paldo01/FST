@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('header', 'Riwayat Peminjaman')
@section('description', 'Daftar peminjaman Anda berdasarkan bulan dan tanggal.')

@section('header_actions')
    <a href="{{ route('peminjaman.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-marun-800 hover:bg-marun-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Peminjaman Ruangan
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filter Tanggal --}}
    <div class="bg-white border border-gray-200 rounded-2xl px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-marun-50 rounded-lg flex items-center justify-center">
                <i data-lucide="calendar" class="w-5 h-5 text-marun-800"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Menampilkan tanggal</p>
                <p class="text-sm font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}
                </p>
            </div>
        </div>
        <form method="GET" action="{{ route('peminjaman.index') }}" class="flex items-center gap-2">
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 text-sm focus:ring-marun-500 focus:border-marun-500" title="Pilih Tanggal">
        </form>
    </div>

    {{-- Statistik Bulan Ini --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalBulanIni }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Peminjaman</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $sedangBerjalan }}</p>
            <p class="text-xs text-gray-500 mt-1">Sedang Berjalan</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-mustard-600">{{ $menunggu }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu Verifikasi</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $selesai }}</p>
            <p class="text-xs text-gray-500 mt-1">Selesai</p>
        </div>
    </div>

    {{-- Timeline per Tanggal --}}
    @if($grouped->isEmpty())
        <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
            <i data-lucide="calendar-x" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
            <h4 class="text-lg font-medium text-gray-900">Tidak ada peminjaman di bulan ini</h4>
            <p class="text-gray-500 text-sm mt-1">Coba pilih bulan lain atau ajukan peminjaman baru.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($grouped as $tanggal => $items)
                {{-- Header Tanggal --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-marun-800 rounded-xl flex items-center justify-center text-white shrink-0">
                        <span class="text-sm font-bold">{{ \Carbon\Carbon::parse($items->first()->tanggal_mulai)->format('d') }}</span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $tanggal }}</p>
                        <p class="text-xs text-gray-500">{{ $items->count() }} peminjaman</p>
                    </div>
                    <div class="flex-1 h-px bg-gray-200 ml-2"></div>
                </div>

                {{-- Kartu Peminjaman pada Tanggal Tersebut --}}
                <div class="ml-5 pl-8 border-l-2 border-gray-100 space-y-3">
                    @foreach($items as $p)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-sm transition-shadow">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">

                                {{-- Ikon Jenis --}}
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0
                                    {{ $p->fasilitas->kategori->jenis === 'ruangan' ? 'bg-marun-50 text-marun-800' : 'bg-mustard-50 text-mustard-700' }}">
                                    <i data-lucide="{{ $p->fasilitas->kategori->jenis === 'ruangan' ? 'door-open' : 'cpu' }}" class="w-5 h-5"></i>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $p->fasilitas->nama_fasilitas }}</h4>
                                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $p->fasilitas->kode_fasilitas }}</span>
                                        <x-badge :status="$p->status_pengajuan" />
                                    </div>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5">
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Diambil: {{ $p->tanggal_mulai->format('H:i') }}
                                        </span>
                                        @if($p->pengembalian)
                                            <span class="text-xs text-green-600 flex items-center gap-1">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                Dikembalikan: {{ $p->pengembalian->tanggal_pengembalian->format('H:i, d M Y') }}
                                            </span>
                                            @php
                                                $durasi = $p->tanggal_mulai->diffForHumans($p->pengembalian->tanggal_pengembalian, true);
                                            @endphp
                                            <span class="text-xs text-blue-600 flex items-center gap-1">
                                                <i data-lucide="timer" class="w-3 h-3"></i>
                                                Durasi: {{ $durasi }}
                                            </span>
                                        @elseif($p->status_pengajuan === 'disetujui')
                                            <span class="text-xs text-orange-600 flex items-center gap-1">
                                                <i data-lucide="loader" class="w-3 h-3"></i>
                                                Belum dikembalikan
                                            </span>
                                        @endif
                                        <span class="text-xs text-gray-500 flex items-center gap-1 capitalize">
                                            <i data-lucide="tag" class="w-3 h-3"></i>
                                            {{ $p->fasilitas->kategori->jenis }}
                                        </span>
                                    </div>
                                    @if($p->tujuan_peminjaman)
                                        <p class="text-xs text-gray-400 mt-1.5 italic truncate max-w-lg">"{{ $p->tujuan_peminjaman }}"</p>
                                    @endif
                                </div>

                                {{-- Aksi --}}
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($p->status_pengajuan === 'selesai' && !$p->ulasan)
                                        <a href="{{ route('ulasan.buat', $p->id) }}"
                                           class="p-2 text-mustard-600 hover:text-mustard-700 hover:bg-mustard-50 rounded-lg transition-colors" title="Beri Ulasan">
                                            <i data-lucide="star" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('peminjaman.show', $p->id) }}"
                                       class="p-2 text-marun-700 hover:text-marun-900 hover:bg-marun-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
