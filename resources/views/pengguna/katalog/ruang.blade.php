@extends('layouts.app')

@section('title', 'Katalog Ruangan')
@section('header', 'Katalog Ruangan')
@section('description', 'Daftar semua ruangan yang tersedia untuk dipinjam.')

@section('content')
<div class="space-y-6">

    {{-- Filter & Summary Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-marun-50 rounded-xl flex items-center justify-center text-marun-800">
                <i data-lucide="door-open" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Ruangan</p>
                <p class="text-lg font-bold text-gray-900">{{ $fasilitas->count() }} Ruangan</p>
            </div>
        </div>
        <a href="{{ route('peminjaman.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-marun-800 text-white text-sm font-medium rounded-xl hover:bg-marun-900 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Peminjaman Ruangan
        </a>
    </div>

    {{-- Kartu Ruangan --}}
    @if($fasilitas->isEmpty())
        <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
            <i data-lucide="building-2" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
            <h4 class="text-lg font-medium text-gray-900">Belum ada ruangan terdaftar</h4>
            <p class="text-gray-500 text-sm mt-1">Data ruangan akan muncul di sini setelah ditambahkan oleh Admin.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($fasilitas as $f)
                @php 
                    $isBorrowed = in_array($f->id, $dipinjamIds); 
                @endphp
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-md transition-all duration-200 flex flex-col {{ $isBorrowed ? 'opacity-60 grayscale hover:-translate-y-0' : 'hover:-translate-y-0.5' }}">
                    {{-- Gambar / Placeholder --}}
                    <div class="h-36 bg-gradient-to-br {{ $isBorrowed ? 'from-gray-100 to-gray-200' : 'from-marun-50 to-marun-100' }} flex items-center justify-center relative">
                        @if($f->gambar)
                            <img src="{{ Storage::url($f->gambar) }}" alt="{{ $f->nama_fasilitas }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="door-open" class="w-14 h-14 {{ $isBorrowed ? 'text-gray-400' : 'text-marun-300' }}"></i>
                        @endif
                        {{-- Badge Status --}}
                        <div class="absolute top-3 right-3">
                            @if($isBorrowed)
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full flex items-center gap-1 border border-yellow-200">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Sedang Dipinjam
                                </span>
                            @elseif($f->status === 'tersedia')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Tersedia
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Tidak Tersedia
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-bold text-gray-900 text-base leading-tight">{{ $f->nama_fasilitas }}</h3>
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded shrink-0">{{ $f->kode_fasilitas }}</span>
                        </div>
                        <p class="text-xs font-medium mb-2 {{ $isBorrowed ? 'text-gray-500' : 'text-marun-700' }}">{{ $f->kategori->nama_kategori }}</p>
                        @if($f->deskripsi)
                            <p class="text-sm text-gray-500 line-clamp-2 flex-1">{{ $f->deskripsi }}</p>
                        @else
                            <p class="text-sm text-gray-400 italic flex-1">Tidak ada deskripsi.</p>
                        @endif

                        <div class="mt-4 pt-3 border-t border-gray-100">
                            @if($isBorrowed)
                                <button disabled class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                    <i data-lucide="clock" class="w-4 h-4"></i> Sedang Dipinjam
                                </button>
                            @elseif($f->status === 'tersedia')
                                <a href="{{ route('peminjaman.create') }}?fasilitas={{ $f->id }}"
                                   class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-marun-800 text-white text-sm font-medium rounded-lg hover:bg-marun-900 transition-colors">
                                    <i data-lucide="calendar-plus" class="w-4 h-4"></i> Pinjam Ruangan Ini
                                </a>
                            @else
                                <button disabled class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-gray-100 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                    <i data-lucide="ban" class="w-4 h-4"></i> Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
