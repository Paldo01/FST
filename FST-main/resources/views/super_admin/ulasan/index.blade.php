@extends('layouts.app')

@section('title', 'Ulasan Layanan')
@section('header', 'Ulasan & Penilaian Layanan')
@section('description', 'Pantau tingkat kepuasan pengguna terhadap fasilitas dan pelayanan.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
    <!-- Ringkasan Rating -->
    <x-card class="lg:col-span-1 bg-marun-900 text-white border-none flex flex-col justify-center items-center py-8">
        <h3 class="text-marun-200 font-medium text-sm mb-2 uppercase tracking-wide">Rata-rata Penilaian</h3>
        <div class="text-5xl font-extrabold text-mustard-400 mb-2">{{ number_format($rataRataRating, 1) }}</div>
        
        <div class="flex gap-1 mb-2 text-mustard-400">
            @for($i = 1; $i <= 5; $i++)
                <i data-lucide="star" class="w-5 h-5 {{ $i <= round($rataRataRating) ? 'fill-mustard-400' : 'text-marun-700' }}"></i>
            @endfor
        </div>
        
        <p class="text-sm text-marun-200 mt-2">Dari total {{ $totalUlasan }} ulasan</p>
    </x-card>

    <!-- Daftar Ulasan -->
    <div class="lg:col-span-3 space-y-4">
        @forelse($ulasan as $u)
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-5 relative overflow-hidden">
                <!-- Aksen warna berdasarkan rating -->
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $u->rating >= 4 ? 'bg-green-500' : ($u->rating == 3 ? 'bg-mustard-500' : 'bg-red-500') }}"></div>
                
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex text-mustard-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" class="w-4 h-4 {{ $i <= $u->rating ? 'fill-mustard-400' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $u->rating }}/5</span>
                        </div>
                        
                        <p class="text-gray-800 text-sm font-medium italic">"{{ $u->komentar ?: 'Tidak ada komentar tertulis.' }}"</p>
                    </div>
                    
                    <div class="sm:text-right shrink-0 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Peminjam</p>
                        <p class="text-sm font-bold text-gray-900">{{ $u->peminjaman->pengguna->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500 mt-1">Fasilitas: <span class="font-medium text-marun-800">{{ $u->peminjaman->fasilitas->nama_fasilitas }}</span></p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-50 text-xs text-gray-400 flex items-center justify-between">
                    <span>Diulas pada: {{ $u->created_at->format('d F Y, H:i') }}</span>
                    <a href="{{ route('peminjaman.show', $u->peminjaman_id) }}" class="text-marun-700 hover:underline">Lihat Detail Peminjaman</a>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <i data-lucide="star-off" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <h4 class="text-lg font-medium text-gray-900">Belum ada ulasan</h4>
                <p class="text-gray-500 text-sm mt-1">Pengguna belum memberikan ulasan terhadap layanan.</p>
            </div>
        @endforelse

        @if($ulasan->hasPages())
            <div class="mt-4">
                {{ $ulasan->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
