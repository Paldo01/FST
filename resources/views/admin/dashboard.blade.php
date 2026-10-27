@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-7 pb-10" x-data="dashboardAdmin()">
    
    <!-- Greeting Banner (Sesuai Mockup) -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-2">
            Selamat Datang, Admin 👋
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Kelola fasilitas dan peminjaman FST
        </p>
    </div>

    <!-- 4 Baris Kartu Statistik (Sesuai Mockup) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- 1. Pengajuan Pending -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-gray-100 shadow-2xs hover:shadow-xs transition-all flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-semibold text-gray-500 leading-none">Pengajuan Pending</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $statistik['menunggu_verifikasi'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- 2. Disetujui -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-gray-100 shadow-2xs hover:shadow-xs transition-all flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-semibold text-gray-500 leading-none">Disetujui</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $statistik['disetujui'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- 3. Ditolak -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-gray-100 shadow-2xs hover:shadow-xs transition-all flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                <i data-lucide="x-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-semibold text-gray-500 leading-none">Ditolak</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $statistik['ditolak'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- 4. Total Peminjaman -->
        <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-gray-100 shadow-2xs hover:shadow-xs transition-all flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-semibold text-gray-500 leading-none">Total Peminjaman</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 mt-1.5 leading-none">{{ $statistik['total_peminjaman'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Bagian Pengajuan Terbaru (Sesuai Mockup) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                Pengajuan Terbaru
            </h2>
            <a href="{{ route('admin.verifikasi.index') }}" 
               class="text-xs sm:text-sm font-bold text-[#801414] border border-[#801414]/30 hover:bg-[#801414]/5 rounded-xl px-3.5 py-1.5 transition-colors flex items-center gap-1">
                <span>Lihat Semua</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- List Card Pengajuan Terbaru -->
        <div class="space-y-3 sm:space-y-4">
            @forelse($pengajuanTerbaru as $p)
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs hover:shadow-xs p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 transition-all">
                    
                    <!-- Bagian Kiri: Profil Pengguna (Avatar, Nama, NIM/NIP, Prodi) -->
                    <div class="flex items-center gap-3.5 min-w-[260px]">
                        <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center shrink-0 border border-rose-100">
                            <i data-lucide="user" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm sm:text-base leading-snug">
                                {{ $p->pengguna->nama_lengkap }} 
                                <span class="font-semibold text-gray-600">({{ ucfirst($p->pengguna->jenis_pengguna ?? 'Pengguna') }})</span>
                            </h4>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                {{ $p->pengguna->jenis_pengguna === 'dosen' ? 'Nip' : 'Nim' }} {{ $p->pengguna->nomor_identitas ?? '-' }} 
                                <span class="text-gray-300 mx-1">|</span> 
                                {{ $p->fasilitas->kategori->nama_kategori ?? 'Sains & Teknologi' }}
                            </p>
                        </div>
                    </div>

                    <!-- Bagian Tengah: Detail Peminjaman (Alat/Ruang, Tanggal, Keperluan) -->
                    <div class="flex-1 lg:border-l lg:border-gray-100 lg:pl-6 space-y-1 text-xs sm:text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i data-lucide="package" class="w-4 h-4 text-gray-400 shrink-0"></i>
                            <span>Peminjaman : <strong class="text-gray-900 font-bold">{{ $p->fasilitas->nama_fasilitas }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400 shrink-0"></i>
                            <span>Tanggal : <span class="text-gray-800 font-medium">{{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d F Y') : '-' }}</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4 text-gray-400 shrink-0"></i>
                            <span class="truncate">Keperluan : <span class="text-gray-800">{{ $p->tujuan_peminjaman ?? '-' }}</span></span>
                        </div>
                    </div>

                    <!-- Bagian Kanan: Badge Status & Tombol Aksi (Sesuai Mockup) -->
                    <div class="flex items-center gap-2.5 shrink-0 self-end lg:self-center pt-2 lg:pt-0 border-t lg:border-t-0 border-gray-100 w-full lg:w-auto justify-end">
                        
                        @if($p->status_pengajuan === 'menunggu')
                            <!-- Status: Pending -->
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100/80 text-amber-800 border border-amber-200">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Pending
                            </span>

                            <!-- Aksi: Terima -->
                            <form method="POST" action="{{ route('admin.verifikasi.setujui', $p->id) }}">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1.5 rounded-xl border border-emerald-500 text-emerald-700 bg-white hover:bg-emerald-50 active:scale-95 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Terima
                                </button>
                            </form>

                            <!-- Aksi: Tolak -->
                            <button type="button" 
                                    @click="openRejectModal({{ $p->id }}, '{{ addslashes($p->fasilitas->nama_fasilitas) }}', '{{ addslashes($p->pengguna->nama_lengkap) }}')"
                                    class="px-3 py-1.5 rounded-xl border border-rose-500 text-rose-700 bg-white hover:bg-rose-50 active:scale-95 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                            </button>

                        @elseif($p->status_pengajuan === 'disetujui')
                            <!-- Status: Disetujui -->
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Disetujui
                            </span>

                            <!-- Aksi: Lihat -->
                            <a href="{{ route('admin.riwayat.index') }}" 
                               class="px-3.5 py-1.5 rounded-xl border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <span>Lihat</span> <span>&rarr;</span>
                            </a>

                        @elseif($p->status_pengajuan === 'ditolak')
                            <!-- Status: Ditolak -->
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100/80 text-rose-800 border border-rose-200">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span> Ditolak
                            </span>

                            <a href="{{ route('admin.riwayat.index') }}" 
                               class="px-3.5 py-1.5 rounded-xl border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <span>Lihat</span> <span>&rarr;</span>
                            </a>

                        @else
                            <!-- Status Lain (cth: Selesai) -->
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100/80 text-blue-800 border border-blue-200">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span> {{ ucfirst($p->status_pengajuan) }}
                            </span>

                            <a href="{{ route('admin.riwayat.index') }}" 
                               class="px-3.5 py-1.5 rounded-xl border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <span>Lihat</span> <span>&rarr;</span>
                            </a>
                        @endif

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 p-8 text-center text-gray-500">
                    <i data-lucide="check-circle" class="w-10 h-10 mx-auto mb-2 text-emerald-500"></i>
                    <p class="font-bold text-gray-800 text-sm">Belum Ada Pengajuan Peminjaman</p>
                    <p class="text-xs text-gray-400 mt-0.5">Semua pengajuan peminjaman ruang dan alat akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Grafik Peminjaman (6 Bulan Terakhir) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 mt-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Grafik Peminjaman Fasilitas</h3>
                <p class="text-xs text-gray-500">Statistik aktivitas peminjaman selama 6 bulan terakhir</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#801414]/10 text-[#801414]">
                FST Analytics
            </span>
        </div>
        <div class="h-64 sm:h-72 w-full relative">
            <canvas id="peminjamanChart"></canvas>
        </div>
    </div>

    <!-- Modal Konfirmasi Penolakan Peminjaman -->
    <div x-show="isRejectModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="isRejectModalOpen = false" 
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 relative transition-all">
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base">Tolak Pengajuan</h3>
                </div>
                <button type="button" @click="isRejectModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs sm:text-sm text-gray-600 mb-4">
                Anda akan menolak pengajuan <strong class="text-gray-900" x-text="rejectData.facility"></strong> oleh <strong class="text-gray-900" x-text="rejectData.user"></strong>. Silakan masukkan alasan penolakan:
            </p>

            <form :action="'{{ url('admin/verifikasi') }}/' + rejectData.id + '/tolak'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="alasan_penolakan" class="block text-xs font-semibold text-gray-700 mb-1">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alasan_penolakan" id="alasan_penolakan" required rows="3"
                              class="w-full p-3 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                              placeholder="Contoh: Jadwal bentrok dengan kegiatan fakultas atau fasilitas sedang perbaikan."></textarea>
                </div>

                <div class="flex gap-2.5 justify-end pt-2">
                    <button type="button" @click="isRejectModalOpen = false" 
                            class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-xs">
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function dashboardAdmin() {
        return {
            isRejectModalOpen: false,
            rejectData: { id: null, facility: '', user: '' },
            openRejectModal(id, facility, user) {
                this.rejectData = { id: id, facility: facility, user: user };
                this.isRejectModalOpen = true;
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('peminjamanChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        
        const rawData = @json($grafikBulanan ?? []);
        
        const labels = rawData.map(item => {
            const date = new Date(item.tahun, item.bulan - 1);
            return date.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
        });
        
        const data = rawData.map(item => item.total);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: data.length ? data : [0, 0, 0, 0, 0, 0],
                    borderColor: '#801414',
                    backgroundColor: 'rgba(128, 20, 20, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#801414',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: '#f1f5f9' }
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
