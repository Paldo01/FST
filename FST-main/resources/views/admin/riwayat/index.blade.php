@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('header', 'Riwayat Peminjaman')
@section('description', 'Semua data peminjaman dari seluruh pengguna, dikelompokkan per tanggal.')

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <div class="bg-white border border-gray-200 rounded-2xl px-5 py-4">
        <form method="GET" action="{{ route('admin.riwayat.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap">
            <div class="flex items-center gap-3 flex-1 flex-wrap">
                <div class="w-9 h-9 bg-marun-50 rounded-lg flex items-center justify-center shrink-0">
                    <i data-lucide="search" class="w-5 h-5 text-marun-800"></i>
                </div>
                
                <!-- Input Pencarian Text -->
                <div class="w-full max-w-xs">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari Nama / NIM / NIP..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-marun-500 focus:border-marun-500">
                </div>

                <!-- Input Tanggal -->
                <div>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 text-sm focus:ring-marun-500 focus:border-marun-500" title="Pilih Tanggal">
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <select name="jenis" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:ring-marun-500 focus:border-marun-500">
                    <option value="">Semua Jenis Fasilitas</option>
                    <option value="ruangan" {{ request('jenis') === 'ruangan' ? 'selected' : '' }}>Ruangan</option>
                    <option value="peralatan" {{ request('jenis') === 'peralatan' ? 'selected' : '' }}>Peralatan / Alat</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total</p>
        </div>
        <div class="bg-white border border-blue-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['berjalan'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Sedang Berjalan</p>
        </div>
        <div class="bg-white border border-yellow-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['menunggu'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu</p>
        </div>
        <div class="bg-white border border-green-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['selesai'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Selesai</p>
        </div>
        <div class="bg-white border border-red-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $stats['ditolak'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Ditolak</p>
        </div>
    </div>

    {{-- Timeline per Tanggal --}}
    @if($grouped->isEmpty())
        <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
            <i data-lucide="calendar-x" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
            <h4 class="text-lg font-medium text-gray-900">Tidak ada data peminjaman di bulan ini</h4>
            <p class="text-gray-500 text-sm mt-1">Coba pilih bulan atau filter yang berbeda.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($grouped as $tanggal => $items)
                {{-- Header Tanggal --}}
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-marun-800 rounded-xl flex flex-col items-center justify-center text-white shrink-0">
                        <span class="text-base font-bold leading-none">{{ \Carbon\Carbon::parse($items->first()->tanggal_mulai)->format('d') }}</span>
                        <span class="text-xs opacity-70">{{ \Carbon\Carbon::parse($items->first()->tanggal_mulai)->format('M') }}</span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $tanggal }}</p>
                        <p class="text-xs text-gray-500">{{ $items->count() }} transaksi peminjaman</p>
                    </div>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                {{-- Daftar Peminjaman hari itu --}}
                <div class="ml-6 pl-8 border-l-2 border-gray-100 space-y-3">
                    @foreach($items as $p)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-sm transition-shadow">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                                {{-- Ikon Jenis --}}
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0
                                    {{ $p->fasilitas->kategori->jenis === 'ruangan' ? 'bg-marun-50 text-marun-800' : 'bg-mustard-50 text-mustard-700' }}">
                                    <i data-lucide="{{ $p->fasilitas->kategori->jenis === 'ruangan' ? 'door-open' : 'cpu' }}" class="w-5 h-5"></i>
                                </div>

                                {{-- Info Peminjaman --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $p->fasilitas->nama_fasilitas }}</h4>
                                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $p->fasilitas->kode_fasilitas }}</span>
                                        <x-badge :status="$p->status_pengajuan" />
                                    </div>

                                    {{-- Peminjam --}}
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                                        <span class="text-sm text-gray-700 font-medium">{{ $p->pengguna->nama_lengkap }}</span>
                                        <span class="text-xs text-gray-400">({{ $p->pengguna->nomor_identitas }})</span>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded capitalize">{{ $p->pengguna->jenis_pengguna ?? 'pengguna' }}</span>
                                    </div>

                                    <div class="flex flex-wrap gap-x-4 gap-y-1">
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
                                                $durasi = $p->tanggal_mulai->diff($p->pengembalian->tanggal_pengembalian);
                                                $durasiLabel = '';
                                                if ($durasi->h > 0) $durasiLabel .= $durasi->h . ' jam ';
                                                if ($durasi->i > 0) $durasiLabel .= $durasi->i . ' menit';
                                                if ($durasiLabel === '') $durasiLabel = 'Kurang dari 1 menit';
                                            @endphp
                                            <span class="text-xs text-blue-600 flex items-center gap-1">
                                                <i data-lucide="timer" class="w-3 h-3"></i>
                                                Durasi: {{ trim($durasiLabel) }}
                                            </span>
                                            <span class="text-xs {{ $p->pengembalian->kondisi_akhir === 'baik' ? 'text-green-600' : 'text-red-600' }} flex items-center gap-1 capitalize">
                                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                                Kondisi: {{ $p->pengembalian->kondisi_akhir }}
                                            </span>
                                        @elseif($p->status_pengajuan === 'disetujui')
                                            <span class="text-xs text-orange-500 flex items-center gap-1">
                                                <i data-lucide="loader" class="w-3 h-3"></i>
                                                Belum dikembalikan
                                            </span>
                                        @endif
                                    </div>

                                    @if($p->tujuan_peminjaman)
                                        <p class="text-xs text-gray-400 italic mt-1 truncate">"{{ $p->tujuan_peminjaman }}"</p>
                                    @endif
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
