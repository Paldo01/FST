@extends('layouts.app')

@section('title', 'Dashboard Super Admin - SIMORA FST')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Super Admin</h1>
            <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->nama_lengkap }}. Pantau seluruh aktivitas sistem SIMORA.</p>
        </div>
        <div class="text-sm text-gray-400 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm">
            <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1 text-marun-700"></i>
            {{ now()->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col gap-2 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pengguna</p>
                <span class="w-8 h-8 rounded-lg bg-marun-50 flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-marun-700"></i>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $statistik['total_pengguna'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Admin Aktif</p>
                <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $statistik['total_admin'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Fasilitas</p>
                <span class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-4 h-4 text-green-600"></i>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $statistik['total_fasilitas'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Peminjaman</p>
                <span class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-purple-600"></i>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $statistik['total_peminjaman'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Bulan Ini</p>
                <span class="w-8 h-8 rounded-lg bg-mustard-50 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4 text-mustard-600"></i>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $statistik['peminjaman_bulan_ini'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rata-rata Rating</p>
                <span class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <i data-lucide="star" class="w-4 h-4 text-yellow-500"></i>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $statistik['rata_rata_rating'] > 0 ? $statistik['rata_rata_rating'] : 'N/A' }}</p>
        </div>
    </div>

    {{-- Grafik & Ulasan Terbaru --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Grafik Tren Peminjaman --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Tren Peminjaman (6 Bulan Terakhir)</h2>
            @if($grafikBulanan->count() > 0)
            <div class="relative h-52">
                <canvas id="grafikPeminjaman"></canvas>
            </div>
            @else
            <div class="flex flex-col items-center justify-center h-52 text-gray-400">
                <i data-lucide="bar-chart-2" class="w-12 h-12 mb-3 opacity-30"></i>
                <p class="text-sm">Belum ada data peminjaman.</p>
            </div>
            @endif
        </div>

        {{-- Ulasan Terbaru --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900">Ulasan Terbaru</h2>
                <a href="{{ route('super_admin.ulasan.index') }}" class="text-xs text-marun-700 hover:underline font-medium">Lihat semua</a>
            </div>
            @forelse($ulasanTerbaru as $ulasan)
            <div class="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
                <div class="w-8 h-8 rounded-full bg-marun-100 text-marun-800 flex items-center justify-center text-xs font-bold shrink-0 uppercase">
                    {{ substr($ulasan->pengguna?->nama_lengkap ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $ulasan->pengguna?->nama_lengkap ?? 'Anonim' }}</p>
                    <div class="flex items-center gap-0.5 my-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-3 h-3 {{ $i <= $ulasan->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-200 fill-gray-200' }}"></i>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500 truncate">{{ $ulasan->komentar ?? '-' }}</p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <i data-lucide="message-square" class="w-8 h-8 mb-2 opacity-30"></i>
                <p class="text-sm">Belum ada ulasan.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">Manajemen Sistem</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('super_admin.pengguna.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-marun-50 hover:border-marun-200 border border-gray-100 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center group-hover:bg-marun-700 transition-colors">
                    <i data-lucide="users" class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-marun-800 text-center">Kelola Admin</span>
            </a>
            <a href="{{ route('super_admin.log.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-marun-50 hover:border-marun-200 border border-gray-100 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center group-hover:bg-marun-700 transition-colors">
                    <i data-lucide="activity" class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-marun-800 text-center">Log Aktivitas</span>
            </a>
            <a href="{{ route('super_admin.ulasan.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-marun-50 hover:border-marun-200 border border-gray-100 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center group-hover:bg-marun-700 transition-colors">
                    <i data-lucide="star" class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-marun-800 text-center">Ulasan Layanan</span>
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-marun-50 hover:border-marun-200 border border-gray-100 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center group-hover:bg-marun-700 transition-colors">
                    <i data-lucide="file-bar-chart" class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-marun-800 text-center">Laporan PDF</span>
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@if($grafikBulanan->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulanMap = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const labels = @json($grafikBulanan->map(fn($d) => bulanMap[$d->bulan - 1] . ' ' . $d->tahun));
    const data   = @json($grafikBulanan->pluck('total'));

    const ctx = document.getElementById('grafikPeminjaman').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data,
                backgroundColor: 'rgba(128,0,0,0.15)',
                borderColor: '#800000',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endif
@endpush
