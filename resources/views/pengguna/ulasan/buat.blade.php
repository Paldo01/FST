@extends('layouts.app')

@section('title', 'Beri Ulasan')
@section('header', 'Ulasan Layanan')
@section('description', 'Beri masukan tentang pengalaman meminjam fasilitas kami.')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        <div class="mb-6 pb-6 border-b border-gray-100 flex gap-4 items-center">
            <div class="w-16 h-16 bg-mustard-50 rounded-xl flex items-center justify-center shrink-0 border border-mustard-100">
                <i data-lucide="star" class="w-8 h-8 text-mustard-500"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $peminjaman->fasilitas->nama_fasilitas }}</h3>
                <p class="text-sm text-gray-500">Peminjaman: {{ $peminjaman->tanggal_mulai->format('d M') }} - {{ $peminjaman->tanggal_selesai->format('d M Y') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('ulasan.simpan', $peminjaman->id) }}" class="space-y-6" x-data="{ rating: 0, hoverRating: 0 }">
            @csrf

            <!-- Rating Stars (Alpine.js) -->
            <div class="text-center py-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Seberapa puas Anda dengan fasilitas/pelayanan kami?</label>
                
                <div class="flex justify-center gap-2">
                    <template x-for="i in 5">
                        <button type="button" @click="rating = i" @mouseenter="hoverRating = i" @mouseleave="hoverRating = 0"
                            class="focus:outline-none transition-transform hover:scale-110">
                            <i data-lucide="star" class="w-10 h-10 transition-colors"
                                :class="(hoverRating >= i || rating >= i) ? 'text-mustard-400 fill-mustard-400' : 'text-gray-200 fill-transparent'"></i>
                        </button>
                    </template>
                </div>
                
                <input type="hidden" name="rating" x-model="rating" required>
                
                <p class="mt-3 text-sm font-medium" :class="rating === 0 ? 'text-gray-400' : 'text-mustard-600'" x-text="
                    rating === 1 ? 'Sangat Buruk' : 
                    rating === 2 ? 'Buruk' : 
                    rating === 3 ? 'Cukup' : 
                    rating === 4 ? 'Baik' : 
                    rating === 5 ? 'Sangat Baik' : 'Pilih Bintang'
                "></p>
                
                @error('rating') <p class="mt-2 text-sm text-red-600">Silakan berikan rating 1-5 bintang.</p> @enderror
            </div>

            <div>
                <label for="komentar" class="block text-sm font-medium text-gray-700 mb-1">Komentar & Masukan (Opsional)</label>
                <textarea name="komentar" id="komentar" rows="4" 
                    class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3 border shadow-sm"
                    placeholder="Apa yang bisa kami tingkatkan? Atau hal apa yang Anda sukai?"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">Lewati</a>
                <button type="submit" :disabled="rating === 0" 
                    class="px-4 py-2 bg-marun-800 text-white font-medium rounded-lg hover:bg-marun-700 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Kirim Ulasan
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
