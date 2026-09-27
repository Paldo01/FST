@extends('layouts.guest')

@section('title', 'Beranda - SIMORA FST')

@push('styles')
<!-- FullCalendar CSS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<style>
    /* Styling FullCalendar to match theme */
    .fc { font-family: inherit; }
    .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 700; color: var(--color-marun-900); }
    .fc .fc-button-primary { background-color: var(--color-marun-800); border-color: var(--color-marun-800); }
    .fc .fc-button-primary:hover, .fc .fc-button-primary:active, .fc .fc-button-primary:focus { background-color: var(--color-marun-700) !important; border-color: var(--color-marun-700) !important; box-shadow: none !important; }
    .fc .fc-daygrid-day.fc-day-today { background-color: var(--color-mustard-50); }
    .fc-event { cursor: pointer; border-radius: 4px; border: none; padding: 2px 4px; font-size: 0.75rem; }
    .fc-h-event .fc-event-main { color: #fff; }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="w-full bg-marun-900 text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                Sistem Manajemen Peminjaman Ruang & Alat Terpadu
            </h1>
            <p class="text-lg md:text-xl text-marun-100 mb-8">
                Fakultas Sains dan Teknologi UIN Imam Bonjol Padang. Cek ketersediaan dan ajukan peminjaman dengan mudah.
            </p>
            
            <!-- Live Search -->
            <div x-data="{ query: '', results: [], show: false, search() {
                if (this.query.length < 2) { this.results = []; this.show = false; return; }
                fetch('/api/fasilitas/cari?q=' + this.query)
                    .then(res => res.json())
                    .then(data => { this.results = data; this.show = true; });
            }}" class="relative max-w-xl mx-auto">
                <div class="relative flex items-center">
                    <i data-lucide="search" class="absolute left-4 w-5 h-5 text-gray-400"></i>
                    <input type="text" x-model="query" @input.debounce.300ms="search" @click.away="show = false" @focus="search"
                        class="w-full pl-12 pr-4 py-4 rounded-full text-gray-900 focus:outline-none focus:ring-4 focus:ring-mustard-500/50 shadow-lg text-lg"
                        placeholder="Cari ruang atau alat...">
                    <button class="absolute right-2 px-6 py-2 bg-mustard-500 hover:bg-mustard-400 text-marun-950 font-bold rounded-full transition-colors">
                        Cari
                    </button>
                </div>
                
                <!-- Search Results Dropdown -->
                <div x-show="show" x-transition style="display: none;" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden text-left z-50">
                    <template x-if="results.length === 0 && query.length >= 2">
                        <div class="p-4 text-gray-500 text-center">Tidak ada hasil ditemukan.</div>
                    </template>
                    <ul class="max-h-80 overflow-y-auto">
                        <template x-for="item in results" :key="item.id">
                            <li class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                                <a href="#kalender" @click="show = false; $dispatch('filter-calendar', {id: item.id})" class="flex items-start p-4 gap-4">
                                    <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center shrink-0">
                                        <i :data-lucide="item.kategori.jenis === 'ruangan' ? 'door-open' : 'monitor'" class="w-6 h-6 text-gray-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900" x-text="item.nama_fasilitas"></h4>
                                        <div class="flex items-center gap-2 mt-1 text-xs">
                                            <span class="text-gray-500" x-text="item.kode_fasilitas"></span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium"
                                                :class="{
                                                    'bg-green-100 text-green-800': item.status === 'tersedia',
                                                    'bg-blue-100 text-blue-800': item.status === 'dipinjam',
                                                    'bg-red-100 text-red-800': item.status === 'perbaikan'
                                                }" x-text="item.status"></span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        @foreach($kategoriList as $kat)
        <div>
            <p class="text-3xl font-extrabold text-marun-800">{{ $kat->fasilitas_count }}</p>
            <p class="text-sm font-medium text-gray-500 mt-1 uppercase">{{ $kat->nama_kategori }}</p>
        </div>
        @endforeach
        <div>
            <p class="text-3xl font-extrabold text-mustard-500">{{ $fasilitasTersedia }}</p>
            <p class="text-sm font-medium text-gray-500 mt-1 uppercase">Tersedia Saat Ini</p>
        </div>
    </div>
</div>

<!-- Kalender Section -->
<div id="kalender" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Jadwal Peminjaman</h2>
            <p class="text-gray-500 mt-2">Lihat jadwal ruangan dan alat yang sedang digunakan atau dibooking.</p>
        </div>
        <div class="flex gap-4 text-sm font-medium">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-600"></span> Disetujui
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span> Menunggu
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 w-full">
        <div id="calendar" class="min-h-[600px]"></div>
    </div>
</div>

<!-- Modal Detail Event -->
<div x-data="{ show: false, event: {} }" 
     x-on:show-event-modal.window="event = $event.detail; show = true"
     class="relative z-50">
    
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" style="display: none;"></div>
    
    <div x-show="show" x-transition style="display: none;" class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div @click.away="show = false" class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold leading-6 text-gray-900" x-text="event.title"></h3>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="mt-2 space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <i data-lucide="user" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Peminjam</p>
                            <p class="font-medium text-gray-900" x-text="event.peminjam"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="clock" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Waktu</p>
                            <p class="font-medium text-gray-900">
                                <span x-text="new Date(event.start).toLocaleString('id-ID')"></span> - 
                                <span x-text="new Date(event.end).toLocaleString('id-ID')"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Status</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium capitalize border" x-bind:class="'badge-' + event.status + ' bg-gray-100 text-gray-800 border-gray-200'" x-text="event.status.replace('_', ' ')"></span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="file-text" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Kegiatan</p>
                            <p class="font-medium text-gray-900" x-text="event.tujuan"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="button" @click="show = false" class="w-full inline-flex justify-center rounded-lg border border-transparent bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            locale: 'id',
            events: '/api/fasilitas/ketersediaan',
            eventClick: function(info) {
                // Dispatch event to AlpineJS Modal
                window.dispatchEvent(new CustomEvent('show-event-modal', {
                    detail: {
                        title: info.event.title,
                        start: info.event.startStr,
                        end: info.event.endStr || info.event.startStr,
                        peminjam: info.event.extendedProps.peminjam,
                        status: info.event.extendedProps.status,
                        tujuan: info.event.extendedProps.tujuan
                    }
                }));
                
                // Re-init lucide icons after rendering modal content
                setTimeout(() => lucide.createIcons(), 50);
            }
        });
        calendar.render();
    });
</script>
@endpush
