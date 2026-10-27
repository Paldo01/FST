@extends('layouts.app')

@section('title', 'Evaluasi Feedback - Super Admin SIMORA FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="{ showDetailModal: false, selectedDetail: null }">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Evaluasi Feedback
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Kelola dan lihat evaluasi feedback dari pengguna setelah menggunakan fasilitas
        </p>
    </div>

    <!-- 3 Kartu Statistik Utama (Real-time dari Database) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        
        <!-- Kartu 1: Rata-rata rating -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#FEE2E2] flex items-center justify-center shrink-0">
                <i data-lucide="star" class="w-6 h-6 sm:w-7 sm:h-7 text-[#EF4444]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Rata-rata rating</p>
                <div class="flex items-baseline gap-1 mt-0.5">
                    <span class="text-2xl sm:text-3xl font-black text-gray-900 leading-none">{{ $rataRataRating }}</span>
                    <span class="text-xl sm:text-2xl font-bold text-gray-900">/ 5</span>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Total Feedback -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#D1FAE5] flex items-center justify-center shrink-0">
                <i data-lucide="message-square" class="w-6 h-6 sm:w-7 sm:h-7 text-[#10B981]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Feedback</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $totalFeedback }}
                </h3>
            </div>
        </div>

        <!-- Kartu 3: Feedback Terbaru -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-6 h-6 sm:w-7 sm:h-7 text-[#F59E0B]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Feedback Terbaru</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $feedbackTerbaru }}
                </h3>
                <p class="text-xs text-gray-400 font-medium mt-1">Dalam 30 hari terakhir</p>
            </div>
        </div>

    </div>

    <!-- Filter Bar (Form Pencarian & Rentang Tanggal) -->
    <form method="GET" action="{{ route('super_admin.ulasan.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
        <!-- Pencarian -->
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input type="text" 
                   name="cari" 
                   value="{{ request('cari') }}" 
                   placeholder="Cari pengguna, fasilitas, atau feedback" 
                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
        </div>

        <!-- Pilih Rentang Tanggal -->
        <div class="relative sm:w-60 shrink-0">
            <input type="date" 
                   name="rentang_tanggal" 
                   value="{{ request('rentang_tanggal') }}" 
                   placeholder="Pilih Tanggal" 
                   class="w-full pl-4 pr-10 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
            <i data-lucide="calendar" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"></i>
        </div>

        <!-- Tombol Filter -->
        <div class="flex items-center gap-2 shrink-0">
            <button type="submit" 
                    class="px-6 py-2.5 sm:py-3 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span>Filter</span>
            </button>

            @if(request('cari') || request('rentang_tanggal'))
                <a href="{{ route('super_admin.ulasan.index') }}" 
                   class="px-4 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl sm:rounded-2xl transition-all flex items-center justify-center gap-1.5"
                   title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>

    <!-- Tabel Evaluasi Feedback (Server-side Rendered) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7">
        
        <div class="overflow-x-auto rounded-xl sm:rounded-2xl border border-gray-100">
            <table class="w-full text-xs sm:text-sm text-left">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-[#E2E8F0]/40 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-3.5 text-center w-12">No</th>
                        <th scope="col" class="px-4 py-3.5 w-32">Pengguna</th>
                        <th scope="col" class="px-4 py-3.5 w-36">Fasilitas</th>
                        <th scope="col" class="px-4 py-3.5 w-36">Rating</th>
                        <th scope="col" class="px-4 py-3.5">Feedback</th>
                        <th scope="col" class="px-4 py-3.5 w-36">Tanggal</th>
                        <th scope="col" class="px-4 py-3.5 text-center w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($feedbacks as $index => $item)
                        @php
                            $userRole = ucfirst($item->pengguna->jenis_pengguna ?? ($item->pengguna->peran === 'super_admin' ? 'Super Admin' : 'Pengguna'));
                            $fasilitasNama = $item->peminjaman->fasilitas->nama_fasilitas ?? 'Fasilitas';
                            $tanggalFormatted = $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-';
                            $modalData = [
                                'pengguna' => $userRole . ($item->pengguna ? ' (' . $item->pengguna->nama_lengkap . ')' : ''),
                                'fasilitas' => $fasilitasNama,
                                'rating' => $item->rating,
                                'feedback' => $item->komentar ?: 'Tidak ada komentar tertulis.',
                                'tanggal' => $tanggalFormatted
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3.5 text-center font-medium text-gray-600">
                                {{ $feedbacks->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900 leading-tight">{{ $userRole }}</div>
                                @if($item->pengguna && $item->pengguna->nama_lengkap)
                                    <div class="text-[11px] text-gray-400 font-normal mt-0.5 truncate max-w-[130px]">
                                        {{ $item->pengguna->nama_lengkap }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-medium text-gray-800">
                                {{ $fasilitasNama }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-0.5">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <svg class="w-4 h-4 {{ $s <= $item->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                        </svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-gray-700 italic">
                                {{ $item->komentar ?: '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 font-mono text-xs">
                                {{ $tanggalFormatted }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <button type="button" 
                                        @click="selectedDetail = @js($modalData); showDetailModal = true;"
                                        class="p-1.5 text-gray-800 hover:text-[#801414] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer" 
                                        title="Lihat Detail Ulasan">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
                                    <i data-lucide="message-square-off" class="w-8 h-8"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800">Belum Ada Feedback</h4>
                                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                                    @if(request('cari') || request('rentang_tanggal'))
                                        Tidak ada evaluasi feedback yang cocok dengan filter pencarian Anda.
                                    @else
                                        Belum ada evaluasi feedback yang masuk dari pengguna. Feedback akan muncul otomatis di sini setelah peminjam menyelesaikan peminjaman dan memberikan ulasan fasilitas.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Info Data -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-5 mt-2 border-t border-gray-100">
            <p class="text-xs sm:text-sm font-medium text-gray-500">
                @if ($feedbacks->total() > 0)
                    Menampilkan {{ $feedbacks->firstItem() }}-{{ $feedbacks->lastItem() }} dari {{ $feedbacks->total() }} data
                @else
                    Menampilkan 0 dari 0 data
                @endif
            </p>

            @if ($feedbacks->hasPages())
                <div class="flex items-center gap-1.5 self-center sm:self-auto">
                    {{-- Previous Page Link --}}
                    @if ($feedbacks->onFirstPage())
                        <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </span>
                    @else
                        <a href="{{ $feedbacks->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center justify-center text-xs font-bold transition-colors">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($feedbacks->getUrlRange(1, $feedbacks->lastPage()) as $page => $url)
                        @if ($page == $feedbacks->currentPage())
                            <span class="w-8 h-8 rounded-lg bg-[#801414] text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center justify-center text-xs font-bold transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($feedbacks->hasMorePages())
                        <a href="{{ $feedbacks->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center justify-center text-xs font-bold transition-colors">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </span>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Detail Evaluasi Feedback -->
    <div x-show="showDetailModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs" @click="showDetailModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sm:p-8 overflow-hidden z-10"
                 @click.stop>
                
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#D1FAE5] flex items-center justify-center shrink-0">
                            <i data-lucide="message-square" class="w-5 h-5 text-[#10B981]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Detail Evaluasi Feedback</h3>
                            <p class="text-xs text-gray-500 font-medium">Ulasan kepuasan penggunaan fasilitas</p>
                        </div>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="space-y-4 pt-5" x-if="selectedDetail">
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Pengguna</p>
                            <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="selectedDetail ? selectedDetail.pengguna : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Fasilitas</p>
                            <p class="text-sm font-bold text-[#801414] mt-0.5" x-text="selectedDetail ? selectedDetail.fasilitas : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Waktu Ulasan</p>
                            <p class="text-xs font-bold text-gray-700 mt-0.5 font-mono" x-text="selectedDetail ? selectedDetail.tanggal : ''"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Nilai Rating</p>
                            <div class="flex items-center gap-1 mt-0.5">
                                <template x-for="star in 5" :key="star">
                                    <svg class="w-4 h-4" :class="star <= (selectedDetail ? selectedDetail.rating : 0) ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200'" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                    </svg>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-700 mb-1.5">Isi Feedback / Komentar Pengguna:</p>
                        <div class="p-4 bg-white border border-gray-200 rounded-2xl text-xs sm:text-sm text-gray-800 italic leading-relaxed"
                             x-text="selectedDetail ? selectedDetail.feedback : ''">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end border-t border-gray-100">
                        <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs sm:text-sm font-bold rounded-xl transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
