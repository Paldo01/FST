@extends('layouts.app')

@section('title', 'Presensi dan Feedback - SIMORA FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2">

    <!-- Header Halaman -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Presensi & Feedback Layanan
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Berikan umpan balik dan evaluasi pelayanan fasilitas yang telah Anda gunakan
        </p>
    </div>

    <!-- Peminjaman yang Perlu Diulas -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 space-y-4">
        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-[#FEF3C7] text-[#D97706] flex items-center justify-center shrink-0">
                <i data-lucide="star" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-black text-gray-900">Perlu Diberikan Ulasan</h2>
                <p class="text-xs text-gray-500 font-medium">Peminjaman Anda yang telah selesai namun belum diberi evaluasi</p>
            </div>
        </div>

        @if($perluDiulas->isEmpty())
            <div class="text-center py-8">
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="check" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-800">Semua Ulasan Telah Lengkap</h4>
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                    Tidak ada peminjaman selesai yang menunggu ulasan Anda saat ini. Terima kasih atas partisipasi Anda!
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($perluDiulas as $item)
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-[#801414] shrink-0">
                                <i data-lucide="calendar-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">{{ $item->fasilitas->nama_fasilitas }}</h4>
                                <p class="text-xs text-gray-500 font-mono">{{ $item->tanggal_selesai->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('ulasan.buat', $item->id) }}" 
                           class="px-4 py-2 bg-[#801414] hover:bg-[#681010] text-white text-xs font-bold rounded-xl transition-all shrink-0">
                            Beri Ulasan
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Riwayat Feedback yang Sudah Diberikan -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 space-y-4">
        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-[#D1FAE5] text-[#10B981] flex items-center justify-center shrink-0">
                <i data-lucide="message-square" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-black text-gray-900">Riwayat Feedback Anda</h2>
                <p class="text-xs text-gray-500 font-medium">Daftar masukan dan evaluasi yang telah Anda kirimkan sebelumnya</p>
            </div>
        </div>

        @if($riwayatUlasan->isEmpty())
            <div class="text-center py-8 text-xs text-gray-500">
                Anda belum pernah mengirimkan ulasan layanan.
            </div>
        @else
            <div class="space-y-3">
                @foreach($riwayatUlasan as $ulasan)
                    <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-900">
                                    {{ $ulasan->peminjaman->fasilitas->nama_fasilitas ?? 'Fasilitas' }}
                                </span>
                                <span class="text-xs text-gray-400 font-mono">
                                    • {{ $ulasan->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-0.5">
                                @for($s = 1; $s <= 5; $s++)
                                    <svg class="w-4 h-4 {{ $s <= $ulasan->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        @if($ulasan->komentar)
                            <p class="text-xs sm:text-sm text-gray-700 italic">"{{ $ulasan->komentar }}"</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="pt-2">
                {{ $riwayatUlasan->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
