@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Pengelola Fasilitas')
@section('description', 'Ringkasan operasional dan persetujuan fasilitas.')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                    <i data-lucide="box" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Fasilitas</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $statistik['total_fasilitas'] }}</h3>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Tersedia</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $statistik['fasilitas_tersedia'] }}</h3>
                </div>
            </div>
        </x-card>

        <x-card class="bg-gradient-to-r from-mustard-500 to-mustard-400 text-marun-950 border-none">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center shrink-0">
                    <i data-lucide="inbox" class="w-6 h-6 text-marun-950"></i>
                </div>
                <div>
                    <p class="text-sm font-medium opacity-90">Perlu Verifikasi</p>
                    <h3 class="text-2xl font-bold">{{ $statistik['menunggu_verifikasi'] }}</h3>
                </div>
            </div>
        </x-card>

        <x-card class="bg-gradient-to-r from-marun-800 to-marun-700 text-white border-none">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <i data-lucide="refresh-cw" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium opacity-90">Pending Pengembalian</p>
                    <h3 class="text-2xl font-bold">{{ $statistik['pengembalian_pending'] }}</h3>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart (2/3 width) -->
        <x-card class="lg:col-span-2">
            <x-slot name="header">
                <h3 class="text-lg font-bold text-gray-900">Statistik Peminjaman (6 Bulan Terakhir)</h3>
            </x-slot>
            <div class="h-80 w-full relative">
                <canvas id="peminjamanChart"></canvas>
            </div>
        </x-card>

        <!-- Needs Verification List (1/3 width) -->
        <x-card class="lg:col-span-1">
            <x-slot name="header">
                <div class="flex justify-between items-center w-full">
                    <h3 class="text-lg font-bold text-gray-900">Perlu Verifikasi</h3>
                    <span class="bg-mustard-100 text-mustard-800 text-xs px-2 py-1 rounded-full font-bold">
                        {{ $menungguVerifikasi->count() }}
                    </span>
                </div>
            </x-slot>
            
            <div class="space-y-4">
                @forelse($menungguVerifikasi->take(5) as $p)
                    <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-marun-900 text-sm truncate pr-2">{{ $p->fasilitas->nama_fasilitas }}</h4>
                            <span class="text-xs text-gray-500 whitespace-nowrap">{{ $p->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mb-1"><span class="font-medium">Oleh:</span> {{ $p->pengguna->nama_lengkap }}</p>
                        <p class="text-xs text-gray-600 mb-3"><span class="font-medium">Tgl:</span> {{ $p->tanggal_mulai->format('d M') }} - {{ $p->tanggal_selesai->format('d M Y') }}</p>
                        
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.verifikasi.setujui', $p->id) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full text-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition-colors">Setujui</button>
                            </form>
                            <a href="{{ route('admin.verifikasi.index') }}" class="flex-1 text-center px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded text-xs font-medium transition-colors">Detail</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-500"></i>
                        <p class="text-sm">Semua pengajuan telah diverifikasi.</p>
                    </div>
                @endforelse
            </div>
            
            @if($menungguVerifikasi->count() > 5)
                <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('admin.verifikasi.index') }}" class="text-sm font-medium text-marun-700 hover:text-marun-900">Lihat Semua ({{ $menungguVerifikasi->count() }})</a>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('peminjamanChart').getContext('2d');
        
        // Data dari Controller
        const rawData = @json($grafikBulanan);
        
        const labels = rawData.map(item => {
            const date = new Date(item.tahun, item.bulan - 1);
            return date.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
        });
        
        const data = rawData.map(item => item.total);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: data,
                    borderColor: '#800000', // Marun
                    backgroundColor: 'rgba(128, 0, 0, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#e5a93c', // Mustard
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#212529',
                        padding: 12,
                        titleFont: { size: 13 },
                        bodyFont: { size: 14, weight: 'bold' },
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: '#f3f4f6', borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush
