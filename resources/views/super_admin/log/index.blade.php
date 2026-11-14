@extends('layouts.app')

@section('title', 'Audit Log - Super Admin SIMORA FST')

@section('content')
@php
    $logsPayload = $logs->map(function ($log) {
        return [
            'id' => $log->id,
            'waktu' => $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-',
            'waktu_lengkap' => $log->created_at ? $log->created_at->translatedFormat('d F Y, H:i:s') . ' WIB' : '-',
            'waktu_relatif' => $log->created_at ? $log->created_at->diffForHumans() : '',
            'aktivitas' => $log->label_tindakan,
            'badge_class' => $log->badge_class,
            'keterangan' => $log->detail_kegiatan ?? 'Tidak ada keterangan tambahan.',
            'ip' => $log->alamat_ip ?? '127.0.0.1',
            'pengguna' => $log->pengguna ? $log->pengguna->nama_lengkap : 'Sistem Otomatis',
            'peran' => $log->pengguna ? ucfirst(str_replace('_', ' ', $log->pengguna->peran)) : 'System',
            'email' => $log->pengguna ? $log->pengguna->email : '-'
        ];
    })->values();
@endphp

<div class="space-y-6 pb-12" x-data="auditLogApp()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Audit Log
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-normal">
            Catatan aktivitas sistem oleh pengguna
        </p>
    </div>

    <!-- Card Utama (Kontainer Filter & Tabel Audit Log Sesuai Mockup) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-200/80 shadow-2xs p-5 sm:p-6 lg:p-7 space-y-6">

        <!-- Toolbar Pencarian & Filter Rentang Tanggal -->
        <form method="GET" action="{{ route('super_admin.log.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
            
            <!-- 1. Input Pencarian -->
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input type="text" 
                       name="cari" 
                       value="{{ request('cari') }}" 
                       placeholder="Cari aktivitas, user, atau keterangan" 
                       class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-white border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0088FF] focus:ring-2 focus:ring-[#0088FF]/20 transition-all shadow-2xs">
            </div>

            <!-- 2. Input Rentang Tanggal -->
            <div class="relative sm:w-64 shrink-0">
                <input type="text" 
                       id="rentang_tanggal" 
                       name="rentang_tanggal" 
                       value="{{ request('rentang_tanggal') }}" 
                       placeholder="Pilih Rentang Tanggal" 
                       class="w-full pl-4 pr-10 py-2.5 sm:py-3 bg-white border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0088FF] focus:ring-2 focus:ring-[#0088FF]/20 transition-all shadow-2xs cursor-pointer">
                <i data-lucide="calendar" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"></i>
            </div>

            <!-- 3. Tombol Aksi (Filter & Reset) -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" 
                        class="px-6 py-2.5 sm:py-3 bg-[#B91C1C] hover:bg-[#991B1B] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span>Filter</span>
                </button>

                @if(request('cari') || request('rentang_tanggal'))
                    <a href="{{ route('super_admin.log.index') }}" 
                       class="px-4 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl transition-all flex items-center justify-center gap-1.5"
                       title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        <!-- Tabel Audit Log (Dukungan Transisi Smooth) -->
        <div id="audit-table-area" 
             class="overflow-x-auto rounded-xl border border-gray-200/80 transition-opacity duration-200 relative"
             :class="loadingPage ? 'opacity-40 pointer-events-none' : 'opacity-100'">
            
            <!-- Loading Indicator Overlay -->
            <div x-show="loadingPage" 
                 x-cloak
                 class="absolute inset-0 bg-white/50 backdrop-blur-2xs flex items-center justify-center z-10 transition-opacity">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-md border border-gray-200 text-xs font-bold text-gray-700">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-[#801414]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat data...
                </div>
            </div>

            <table class="w-full text-xs sm:text-sm text-left">
                <!-- Header Tabel Sesuai Mockup -->
                <thead class="text-sm font-bold text-gray-800 bg-[#F1F5F9]/80 border-b border-gray-200/80">
                    <tr>
                        <th scope="col" class="px-4 py-3.5 sm:py-4 text-center w-14 font-bold text-gray-800">No</th>
                        <th scope="col" class="px-4 py-3.5 sm:py-4 w-44 font-bold text-gray-800">Waktu</th>
                        <th scope="col" class="px-4 py-3.5 sm:py-4 w-56 font-bold text-gray-800">Aktivitas</th>
                        <th scope="col" class="px-4 py-3.5 sm:py-4 font-bold text-gray-800">Keterangan</th>
                        <th scope="col" class="px-4 py-3.5 sm:py-4 text-center w-20 font-bold text-gray-800">Aksi</th>
                    </tr>
                </thead>

                <!-- Body Tabel -->
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($logs as $index => $log)
                        @php
                            $rowNum = $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage();
                            $waktuFormatted = $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-';
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <!-- No -->
                            <td class="px-4 py-3.5 sm:py-4 text-center text-sm sm:text-base font-normal text-gray-800">
                                {{ $rowNum }}
                            </td>

                            <!-- Waktu -->
                            <td class="px-4 py-3.5 sm:py-4 text-sm sm:text-base font-normal text-gray-800 whitespace-nowrap">
                                {{ $waktuFormatted }}
                            </td>

                            <!-- Aktivitas (Badge Pill Sesuai Mockup) -->
                            <td class="px-4 py-3.5 sm:py-4">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap {{ $log->badge_class }}">
                                    {{ $log->label_tindakan }}
                                </span>
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-3.5 sm:py-4 text-sm sm:text-base text-gray-700 font-normal leading-relaxed">
                                {{ $log->detail_kegiatan ?? '-' }}
                            </td>

                            <!-- Aksi (Tombol Detail Mata) -->
                            <td class="px-4 py-3.5 sm:py-4 text-center">
                                <button type="button" 
                                        @click="openDetail({{ $log->id }})"
                                        class="p-2 text-gray-900 hover:text-[#B91C1C] hover:bg-gray-100 rounded-xl transition-all cursor-pointer inline-flex items-center justify-center active:scale-90"
                                        title="Lihat Detail Log">
                                    <i data-lucide="eye" class="w-5 h-5 text-gray-900"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                        <i data-lucide="inbox" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-600">Tidak ada catatan aktivitas yang ditemukan</p>
                                    @if(request('cari') || request('rentang_tanggal'))
                                        <a href="{{ route('super_admin.log.index') }}" class="text-xs text-[#801414] font-bold hover:underline">
                                            Reset Filter Pencarian
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Info Jumlah Data & Tombol Navigasi Pagination Sesuai Screenshot -->
        <div id="audit-pagination-area" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <!-- Informasi Data -->
            <div class="text-sm text-slate-500 font-normal">
                @if($logs->total() > 0)
                    Menampilkan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} dari {{ $logs->total() }} data
                @else
                    Menampilkan 0 data
                @endif
            </div>

            <!-- Tombol Pagination Kustom (Sesuai Screenshot: [<] [1] [2] [3] [4] [>]) -->
            @if($logs->hasPages())
                <div class="flex items-center gap-2">
                    {{-- Previous Page Link [<] --}}
                    @if ($logs->onFirstPage())
                        <span class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-300 cursor-not-allowed shadow-2xs">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" 
                           @click.prevent="navigatePage('{{ $logs->previousPageUrl() }}')"
                           class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 hover:border-[#801414] hover:text-[#801414] hover:bg-red-50/30 hover:scale-105 active:scale-95 shadow-2xs transition-all duration-200 cursor-pointer">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </a>
                    @endif

                    {{-- Page Numbers [1] [2] [3] [4] --}}
                    @php
                        $currentPage = $logs->currentPage();
                        $lastPage = $logs->lastPage();
                        $maxButtons = 4;
                        $startPage = max(1, min($currentPage - 1, $lastPage - $maxButtons + 1));
                        $endPage = min($lastPage, $startPage + $maxButtons - 1);
                        if ($startPage < 1) $startPage = 1;
                    @endphp

                    @for ($p = $startPage; $p <= $endPage; $p++)
                        @if ($p == $currentPage)
                            <span class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl bg-[#B91C1C] text-white font-bold text-xs sm:text-sm shadow-xs ring-2 ring-[#B91C1C]/20 select-none">
                                {{ $p }}
                            </span>
                        @else
                            <a href="{{ $logs->url($p) }}" 
                               @click.prevent="navigatePage('{{ $logs->url($p) }}')"
                               class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 hover:border-[#801414] hover:text-[#801414] hover:bg-red-50/30 hover:scale-105 active:scale-95 font-medium text-xs sm:text-sm shadow-2xs transition-all duration-200 cursor-pointer">
                                {{ $p }}
                            </a>
                        @endif
                    @endfor

                    {{-- Next Page Link [>] --}}
                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" 
                           @click.prevent="navigatePage('{{ $logs->nextPageUrl() }}')"
                           class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 hover:border-[#801414] hover:text-[#801414] hover:bg-red-50/30 hover:scale-105 active:scale-95 shadow-2xs transition-all duration-200 cursor-pointer">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <span class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-300 cursor-not-allowed shadow-2xs">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </span>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Detail Audit Log (Teleported ke <body> agar terbebas dari overflow parent & blur freeze) -->
    <template x-teleport="body">
        <div x-show="showDetailModal" 
             x-cloak
             class="fixed inset-0 z-[9999] overflow-y-auto"
             style="display: none;"
             @keydown.escape.window="closeDetail()">
            
            <!-- Backdrop Transparan Gelap dengan Efek Blur Halus -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 @click="closeDetail()"></div>

            <!-- Kotak Dialog Modal Berada di Tengah Layar -->
            <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
                <div x-show="showDetailModal"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                     class="pointer-events-auto relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 p-6 sm:p-7 space-y-5"
                     @click.stop>
                    
                    <!-- Header Modal -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-red-50 text-[#801414] flex items-center justify-center shrink-0">
                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 tracking-tight">Detail Audit Log</h3>
                                <p class="text-xs text-gray-500 font-medium">Informasi riwayat aktivitas dalam sistem</p>
                            </div>
                        </div>
                        <button type="button" @click="closeDetail()" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Isi Modal -->
                    <template x-if="selectedLog">
                        <div class="space-y-4 text-xs sm:text-sm">
                            
                            <!-- Aktivitas & Waktu -->
                            <div class="bg-gray-50/90 rounded-2xl p-4 border border-gray-100 space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Aktivitas</span>
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs sm:text-sm font-bold shadow-2xs"
                                          :class="selectedLog.badge_class"
                                          x-text="selectedLog.aktivitas">
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-200/60">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Tercatat</span>
                                    <div class="text-right">
                                        <p class="text-xs sm:text-sm font-bold text-gray-900" x-text="selectedLog.waktu_lengkap"></p>
                                        <p class="text-[11px] text-gray-400 font-medium mt-0.5" x-text="selectedLog.waktu_relatif"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pengguna / Aktor -->
                            <div class="bg-gray-50/90 rounded-2xl p-4 border border-gray-100 space-y-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Pelaku / Pengguna</span>
                                <div class="flex items-center gap-3.5 pt-1">
                                    <div class="w-11 h-11 rounded-2xl bg-[#801414] text-white font-black flex items-center justify-center shrink-0 text-sm shadow-xs">
                                        <span x-text="selectedLog.pengguna ? selectedLog.pengguna.substring(0, 2).toUpperCase() : 'SY'"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-black text-gray-900 truncate" x-text="selectedLog.pengguna"></p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-white border border-gray-200 text-[11px] font-bold text-gray-700 shadow-2xs" x-text="selectedLog.peran"></span>
                                            <span class="text-xs text-gray-500 truncate" x-text="selectedLog.email"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan / Detail Kegiatan -->
                            <div class="bg-gray-50/90 rounded-2xl p-4 border border-gray-100 space-y-1.5">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Keterangan Aktivitas</span>
                                <div class="p-3.5 bg-white rounded-xl border border-gray-200 text-xs sm:text-sm text-gray-800 font-medium leading-relaxed"
                                     x-text="selectedLog.keterangan">
                                </div>
                            </div>

                            <!-- Info Teknis (Alamat IP & ID Log) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50/90 rounded-2xl p-3.5 border border-gray-100">
                                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Alamat IP</span>
                                    <span class="font-mono text-xs sm:text-sm font-bold text-gray-800 mt-1 block" x-text="selectedLog.ip"></span>
                                </div>
                                <div class="bg-gray-50/90 rounded-2xl p-3.5 border border-gray-100">
                                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">ID Record</span>
                                    <span class="font-mono text-xs sm:text-sm font-bold text-gray-800 mt-1 block" x-text="'#' + selectedLog.id"></span>
                                </div>
                            </div>

                        </div>
                    </template>

                    <!-- Tombol Tutup -->
                    <div class="pt-2">
                        <button type="button" 
                                @click="closeDetail()"
                                class="w-full py-3 bg-[#801414] hover:bg-[#681010] active:scale-[0.99] text-white text-xs sm:text-sm font-bold rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span>Tutup</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Styling Flatpickr Rentang Tanggal Kalender Sesuai Tema */
    .flatpickr-calendar {
        border-radius: 1.25rem !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #E2E8F0 !important;
        overflow: hidden;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange {
        background: #801414 !important;
        border-color: #801414 !important;
        color: #ffffff !important;
    }
    .flatpickr-day.inRange {
        background: #FEE2E2 !important;
        border-color: #FEE2E2 !important;
        color: #991B1B !important;
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script>
    function auditLogApp() {
        return {
            showDetailModal: false,
            selectedLog: null,
            logsList: @json($logsPayload),
            loadingPage: false,

            navigatePage(url) {
                if (!url || this.loadingPage) return;
                this.loadingPage = true;
                
                fetch(url, { 
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    } 
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('#audit-table-area');
                    const newPagination = doc.querySelector('#audit-pagination-area');
                    
                    // Parse logsList payload baru dari script HTML yang diambil
                    const scripts = doc.querySelectorAll('script');
                    for (let s of scripts) {
                        if (s.textContent.includes('logsList:')) {
                            const match = s.textContent.match(/logsList:\s*(\[[\s\S]*?\]),\s*loadingPage/);
                            if (match) {
                                try {
                                    this.logsList = JSON.parse(match[1]);
                                } catch (e) {}
                            }
                        }
                    }

                    if (newTable && newPagination) {
                        document.querySelector('#audit-table-area').innerHTML = newTable.innerHTML;
                        document.querySelector('#audit-pagination-area').innerHTML = newPagination.innerHTML;
                        window.history.pushState(null, '', url);
                        
                        this.$nextTick(() => {
                            if (window.lucide) {
                                lucide.createIcons();
                            }
                        });
                    } else {
                        window.location.href = url;
                    }
                })
                .catch(() => {
                    window.location.href = url;
                })
                .finally(() => {
                    this.loadingPage = false;
                });
            },

            openDetail(id) {
                const found = this.logsList.find(item => item.id === id);
                if (found) {
                    this.selectedLog = found;
                    this.showDetailModal = true;
                    this.$nextTick(() => {
                        if (window.lucide) {
                            lucide.createIcons();
                        }
                    });
                }
            },

            closeDetail() {
                this.showDetailModal = false;
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#rentang_tanggal", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            altInputClass: "w-full pl-4 pr-10 py-2.5 sm:py-3 bg-white border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0088FF] focus:ring-2 focus:ring-[#0088FF]/20 transition-all shadow-2xs cursor-pointer",
            locale: "id"
        });
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush
