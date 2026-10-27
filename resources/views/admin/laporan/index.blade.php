@extends('layouts.app')

@section('title', 'Laporan & Statistik - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="laporanHandler()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Laporan & Statistik
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Pantau Statistik Penggunaan ruang dan alat FST
        </p>
    </div>

    <!-- 4 Kartu Statistik (Sesuai Mockup) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Kartu 1: Total Peminjaman -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-6 h-6 sm:w-7 sm:h-7 text-[#EF4444]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Peminjaman</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5">
                    {{ $statistik['total_peminjaman'] }}
                </h3>
            </div>
        </div>

        <!-- Kartu 2: Ruang Terpakai -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#DBEAFE] flex items-center justify-center shrink-0">
                <i data-lucide="door-open" class="w-6 h-6 sm:w-7 sm:h-7 text-[#2563EB]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Ruang Terpakai</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5">
                    {{ $statistik['ruang_terpakai'] }}
                </h3>
            </div>
        </div>

        <!-- Kartu 3: Alat Dipinjam -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <i data-lucide="package" class="w-6 h-6 sm:w-7 sm:h-7 text-[#D97706]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Alat Dipinjam</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5">
                    {{ $statistik['alat_dipinjam'] }}
                </h3>
            </div>
        </div>

        <!-- Kartu 4: Total Pengguna -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#E0F2FE] flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6 sm:w-7 sm:h-7 text-[#0284C7]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Pengguna</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5">
                    {{ $statistik['total_pengguna'] }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Bagian Utama: Grafik & Ringkasan Fasilitas (Sesuai Mockup) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- Kolom Kiri: Statistik Penggunaan (lg:col-span-8) -->
        <div class="lg:col-span-8 bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 flex flex-col justify-between">
            <!-- Header Kartu: Judul & Filter -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100/60">
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                    Statistik Penggunaan
                </h2>

                <div class="flex items-center gap-3">
                    <!-- Filter Periode (Bulan Ini) -->
                    <div class="relative">
                        <button type="button" 
                                @click="togglePeriodDropdown()" 
                                class="flex items-center gap-2.5 px-3.5 py-1.5 sm:py-2 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 hover:border-gray-300 transition-colors shadow-2xs cursor-pointer">
                            <span x-text="selectedPeriodText">Bulan Ini</span>
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                        </button>
                        
                        <!-- Dropdown Menu Periode -->
                        <div x-show="showPeriodDropdown" 
                             @click.outside="showPeriodDropdown = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-2xl shadow-lg z-20 py-1 text-xs sm:text-sm font-medium text-gray-700 overflow-hidden"
                             style="display: none;">
                            <button type="button" @click="setPeriod('Bulan Ini')" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Bulan Ini</button>
                            <button type="button" @click="setPeriod('Bulan Lalu')" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Bulan Lalu</button>
                            <button type="button" @click="setPeriod('Tahun Ini')" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Tahun Ini</button>
                            <button type="button" @click="setPeriod('Semua')" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Semua</button>
                        </div>
                    </div>

                    <!-- Filter Jenis (Semua Jenis) -->
                    <div class="relative">
                        <button type="button" 
                                @click="toggleTypeDropdown()" 
                                class="flex items-center gap-2.5 px-3.5 py-1.5 sm:py-2 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 hover:border-gray-300 transition-colors shadow-2xs cursor-pointer">
                            <span x-text="selectedTypeText">Semua Jenis</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                        </button>

                        <!-- Dropdown Menu Jenis -->
                        <div x-show="showTypeDropdown" 
                             @click.outside="showTypeDropdown = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-2xl shadow-lg z-20 py-1 text-xs sm:text-sm font-medium text-gray-700 overflow-hidden"
                             style="display: none;">
                            <button type="button" @click="setType('Semua Jenis')" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Semua Jenis</button>
                            <button type="button" @click="setType('Ruang')" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Ruang</button>
                            <button type="button" @click="setType('Alat')" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Alat</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Area Grafik Canvas Chart.js -->
            <div class="relative w-full h-72 sm:h-80 md:h-96 pt-6 pb-2">
                <canvas id="usageChart"></canvas>
            </div>

            <!-- Legend di Bagian Bawah Sesuai Mockup (Merah: Ruang, Biru: Alat) -->
            <div class="flex items-center justify-center gap-8 pt-4 border-t border-gray-100/60">
                <div class="flex items-center gap-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-[#DC2626]"></span>
                    <span class="text-xs sm:text-sm font-bold text-gray-800">Ruang</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-[#2563EB]"></span>
                    <span class="text-xs sm:text-sm font-bold text-gray-800">Alat</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Fasilitas & Tombol Cetak PDF (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 flex flex-col justify-between">
            <div class="space-y-5">
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                    Ringkasan Fasilitas
                </h2>

                <!-- Box 1: Ruang (Sesuai Mockup) -->
                <div class="border border-gray-100 rounded-2xl p-4 sm:p-5 bg-white shadow-2xs transition-all hover:border-gray-200">
                    <div class="flex items-center gap-3 mb-3.5">
                        <div class="w-10 h-10 rounded-xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                            <i data-lucide="door-open" class="w-5 h-5 text-[#EF4444]"></i>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900">Ruang</h3>
                    </div>
                    <ul class="space-y-2.5 pl-3">
                        <li class="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-gray-800">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626] shrink-0"></span>
                            <span>Aula FST</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-gray-800">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626] shrink-0"></span>
                            <span>Ruang Rapat</span>
                        </li>
                    </ul>
                </div>

                <!-- Box 2: Alat (Sesuai Mockup) -->
                <div class="border border-gray-100 rounded-2xl p-4 sm:p-5 bg-white shadow-2xs transition-all hover:border-gray-200">
                    <div class="flex items-center gap-3 mb-3.5">
                        <div class="w-10 h-10 rounded-xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-5 h-5 text-[#D97706]"></i>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900">Alat</h3>
                    </div>
                    <ul class="space-y-2.5 pl-3">
                        <li class="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-gray-800">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D97706] shrink-0"></span>
                            <span>Infocus</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-gray-800">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D97706] shrink-0"></span>
                            <span>Sound System</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tombol Cetak Laporan PDF (Sesuai Mockup) -->
            <div class="pt-6">
                <button type="button" 
                        @click="openModalPdf = true"
                        class="w-full py-3.5 px-5 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white font-bold text-sm sm:text-base rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                    <i data-lucide="file-down" class="w-5 h-5"></i>
                    <span>Cetak Laporan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Cetak Laporan PDF -->
    <div x-show="openModalPdf" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs" @click="openModalPdf = false"></div>

        <!-- Modal Dialog -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sm:p-8 overflow-hidden z-10"
                 @click.stop>
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                            <i data-lucide="file-down" class="w-5 h-5 text-[#801414]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Cetak Laporan PDF</h3>
                            <p class="text-xs text-gray-500 font-medium">Pilih periode dan filter data laporan</p>
                        </div>
                    </div>
                    <button type="button" @click="openModalPdf = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Form Filter Cetak -->
                <form method="GET" action="{{ route('admin.laporan.cetak') }}" target="_blank" class="space-y-4 pt-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_awal" class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Mulai</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" required value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                        <div>
                            <label for="tanggal_akhir" class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" required value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                    </div>

                    <div>
                        <label for="jenis_fasilitas" class="block text-xs font-bold text-gray-700 mb-1.5">Jenis Fasilitas</label>
                        <select name="jenis_fasilitas" id="jenis_fasilitas" 
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                            <option value="">Semua Jenis Fasilitas</option>
                            <option value="ruangan">Khusus Ruangan</option>
                            <option value="peralatan">Khusus Peralatan</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-700 mb-1.5">Status Peminjaman</label>
                        <select name="status" id="status" 
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                            <option value="">Semua Status</option>
                            <option value="selesai">Selesai / Dikembalikan</option>
                            <option value="disetujui">Disetujui / Sedang Berjalan</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="menunggu">Menunggu Verifikasi</option>
                        </select>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="openModalPdf = false" 
                                class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                @click="setTimeout(() => openModalPdf = false, 500)"
                                class="px-5 py-2.5 bg-[#801414] hover:bg-[#681010] text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm hover:shadow transition-all flex items-center gap-2">
                            <i data-lucide="file-down" class="w-4 h-4"></i>
                            <span>Unduh Laporan PDF</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function laporanHandler() {
    return {
        showPeriodDropdown: false,
        showTypeDropdown: false,
        openModalPdf: false,
        selectedPeriodText: 'Bulan Ini',
        selectedTypeText: 'Semua Jenis',
        chartInstance: null,

        init() {
            this.$nextTick(() => {
                this.initChart();
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        togglePeriodDropdown() {
            this.showPeriodDropdown = !this.showPeriodDropdown;
            this.showTypeDropdown = false;
        },

        toggleTypeDropdown() {
            this.showTypeDropdown = !this.showTypeDropdown;
            this.showPeriodDropdown = false;
        },

        setPeriod(period) {
            this.selectedPeriodText = period;
            this.showPeriodDropdown = false;
            
            // Variasi data periodik untuk efek dinamis jika filter diubah
            if (!this.chartInstance) return;

            if (period === 'Bulan Ini') {
                this.chartInstance.data.datasets[0].data = [18, 12, 2, 1];
                this.chartInstance.data.datasets[1].data = [2, 3, 15, 10];
            } else if (period === 'Bulan Lalu') {
                this.chartInstance.data.datasets[0].data = [14, 10, 3, 2];
                this.chartInstance.data.datasets[1].data = [4, 2, 12, 8];
            } else if (period === 'Tahun Ini') {
                this.chartInstance.data.datasets[0].data = [20, 16, 5, 4];
                this.chartInstance.data.datasets[1].data = [6, 7, 18, 14];
            } else {
                this.chartInstance.data.datasets[0].data = [18, 12, 2, 1];
                this.chartInstance.data.datasets[1].data = [2, 3, 15, 10];
            }
            this.chartInstance.update();
        },

        setType(type) {
            this.selectedTypeText = type;
            this.showTypeDropdown = false;

            if (!this.chartInstance) return;

            if (type === 'Ruang') {
                this.chartInstance.getDatasetMeta(0).hidden = false;
                this.chartInstance.getDatasetMeta(1).hidden = true;
            } else if (type === 'Alat') {
                this.chartInstance.getDatasetMeta(0).hidden = true;
                this.chartInstance.getDatasetMeta(1).hidden = false;
            } else {
                this.chartInstance.getDatasetMeta(0).hidden = false;
                this.chartInstance.getDatasetMeta(1).hidden = false;
            }
            this.chartInstance.update();
        },

        initChart() {
            const canvas = document.getElementById('usageChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            // Custom Plugin untuk menampilkan angka di atas setiap bar (Sesuai Mockup)
            const valueLabelsPlugin = {
                id: 'valueLabels',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((bar, index) => {
                                const val = dataset.data[index];
                                if (val !== undefined && val !== null) {
                                    ctx.save();
                                    ctx.font = 'bold 12px Inter, sans-serif';
                                    ctx.fillStyle = '#111827';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.fillText(val, bar.x, bar.y - 4);
                                    ctx.restore();
                                }
                            });
                        }
                    });
                }
            };

            this.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Ruang',
                            data: @json($chartData['ruang']),
                            backgroundColor: '#DC2626',
                            borderRadius: { topLeft: 4, topRight: 4 },
                            borderSkipped: false,
                            barPercentage: 0.65,
                            categoryPercentage: 0.55
                        },
                        {
                            label: 'Alat',
                            data: @json($chartData['alat']),
                            backgroundColor: '#2563EB',
                            borderRadius: { topLeft: 4, topRight: 4 },
                            borderSkipped: false,
                            barPercentage: 0.65,
                            categoryPercentage: 0.55
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 20
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 20,
                            ticks: {
                                stepSize: 5,
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#111827'
                            },
                            grid: {
                                color: '#F3F4F6',
                                drawTicks: false
                            },
                            border: {
                                display: true,
                                color: '#111827',
                                width: 1.5
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '700'
                                },
                                color: '#111827'
                            },
                            border: {
                                display: true,
                                color: '#111827',
                                width: 1.5
                            }
                        }
                    }
                },
                plugins: [valueLabelsPlugin]
            });
        }
    };
}
</script>
@endsection
