@extends('layouts.app')

@section('title', 'Laporan dan Statistik - Super Admin SIMORA FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="superAdminLaporanHandler()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                Laporan dan Statistik
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Ringkasan data peminjaman fasilitas FST dalam bentuk grafik dan laporan
            </p>
        </div>

        <!-- Tombol Cetak Laporan PDF (Sesuai Mockup) -->
        <button type="button" 
                @click="openModalPdf = true"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 sm:py-3 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-2xl shadow-sm hover:shadow-md transition-all shrink-0 cursor-pointer">
            <i data-lucide="file-down" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span>Cetak Laporan PDF</span>
        </button>
    </div>

    <!-- 4 Kartu Statistik (Sesuai Mockup) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Kartu 1: Total Admin -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6 sm:w-7 sm:h-7 text-[#EF4444]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Admin</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['total_admin'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Admin</span>
            </div>
        </div>

        <!-- Kartu 2: Total Peminjaman -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#DBEAFE] flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-6 h-6 sm:w-7 sm:h-7 text-[#2563EB]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Peminjaman</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['total_peminjaman'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Peminjaman</span>
            </div>
        </div>

        <!-- Kartu 3: Ruang Terpakai -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#D1FAE5] flex items-center justify-center shrink-0">
                <i data-lucide="door-open" class="w-6 h-6 sm:w-7 sm:h-7 text-[#10B981]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Ruang Terpakai</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['ruang_terpakai'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Ruang</span>
            </div>
        </div>

        <!-- Kartu 4: Alat dipinjam -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5 flex items-center gap-4 transition-all hover:shadow-xs">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FEF3C7] flex items-center justify-center shrink-0">
                <i data-lucide="package" class="w-6 h-6 sm:w-7 sm:h-7 text-[#F59E0B]"></i>
            </div>
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-500">Alat dipinjam</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mt-0.5 leading-none">
                    {{ $statistik['alat_dipinjam'] }}
                </h3>
                <span class="text-xs font-semibold text-gray-400 mt-1 inline-block">Alat</span>
            </div>
        </div>

    </div>

    <!-- Bagian Tengah: Dua Grafik (Grafik Batang Per Bulan & Persentase Donat Sesuai Mockup) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- Grafik Kiri: Grafik Peminjaman Per Bulan (lg:col-span-8) -->
        <div class="lg:col-span-8 bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 flex flex-col justify-between">
            <div>
                <!-- Header Grafik: Judul & Dropdown Tahun -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100/70">
                    <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                        Grafik Peminjaman Per Bulan
                    </h2>

                    <!-- Filter Tahun (Tahun 2026) -->
                    <div class="relative">
                        <button type="button" 
                                @click="showYearDropdown = !showYearDropdown"
                                class="flex items-center gap-2 px-3.5 py-1.5 sm:py-2 bg-white border border-gray-200/90 rounded-xl text-xs sm:text-sm font-semibold text-gray-700 hover:border-gray-300 transition-colors shadow-2xs cursor-pointer">
                            <span x-text="'Tahun ' + selectedYear">Tahun 2026</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </button>
                        
                        <div x-show="showYearDropdown" 
                             @click.outside="showYearDropdown = false" 
                             x-transition 
                             class="absolute right-0 mt-2 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-20 py-1 text-xs sm:text-sm font-medium text-gray-700"
                             style="display: none;">
                            <button type="button" @click="changeYear(2026)" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Tahun 2026</button>
                            <button type="button" @click="changeYear(2025)" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Tahun 2025</button>
                            <button type="button" @click="changeYear(2024)" class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors">Tahun 2024</button>
                        </div>
                    </div>
                </div>

                <!-- Legenda Grafik Sesuai Mockup (Ruang Merah Magenta, Alat Biru) -->
                <div class="flex items-center justify-center gap-6 pt-3 pb-1">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-xs bg-[#E11D48]"></span>
                        <span class="text-xs font-bold text-gray-700">Ruang</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-xs bg-[#2563EB]"></span>
                        <span class="text-xs font-bold text-gray-700">Alat</span>
                    </div>
                </div>

                <!-- Area Canvas Chart.js Batang Bulanan -->
                <div class="relative w-full h-64 sm:h-72 md:h-80 pt-2">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Kanan: Persentase Penggunaan Fasilitas (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7 flex flex-col justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-black text-gray-900 tracking-tight mb-4">
                    Persentase Penggunaan Fasilitas
                </h2>

                <!-- Area Donut Chart dengan Label Tengah -->
                <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-center gap-4 pt-2">
                    <div class="relative w-44 h-44 sm:w-48 sm:h-48 shrink-0 flex items-center justify-center">
                        <canvas id="donutChart"></canvas>
                        <!-- Teks Tengah Total Peminjaman -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                            <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Total</span>
                            <span class="text-xl sm:text-2xl font-black text-gray-900 leading-none my-0.5">20</span>
                            <span class="text-[10px] text-gray-400 font-medium">Peminjaman</span>
                        </div>
                    </div>

                    <!-- Legenda Rinci Sesuai Mockup -->
                    <div class="space-y-3.5 w-full">
                        <div class="flex items-start gap-2.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#3B82F6] shrink-0 mt-0.5"></span>
                            <div>
                                <p class="text-xs sm:text-sm font-bold text-gray-900">Ruang</p>
                                <p class="text-xs text-gray-500 font-medium">12 Peminjaman 60%</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#10B981] shrink-0 mt-0.5"></span>
                            <div>
                                <p class="text-xs sm:text-sm font-bold text-gray-900">Alat</p>
                                <p class="text-xs text-gray-500 font-medium">5 Peminjaman 25%</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#F59E0B] shrink-0 mt-0.5"></span>
                            <div>
                                <p class="text-xs sm:text-sm font-bold text-gray-900">Lainnya</p>
                                <p class="text-xs text-gray-500 font-medium">3 Peminjaman 15%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan footer singkat -->
            <div class="pt-4 mt-2 border-t border-gray-100 text-[11px] text-gray-400 text-center">
                Berdasarkan rekapitulasi data peminjaman terverifikasi
            </div>
        </div>

    </div>

    <!-- Bagian Bawah: Data Peminjaman Terbaru (Sesuai Mockup) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7">
        
        <!-- Header Tabel: Judul, Dropdown Status, Pencarian -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-gray-100">
            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                Data Peminjaman Terbaru
            </h2>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Dropdown Filter Semua Status -->
                <div class="relative sm:w-40 shrink-0">
                    <select x-model="statusFilter" 
                            class="w-full appearance-none px-4 py-2 bg-white border border-gray-200/90 rounded-xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer pr-9">
                        <option value="">Semua</option>
                        <option value="Pending">Pending</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                </div>

                <!-- Input Pencarian -->
                <div class="relative sm:w-64 shrink-0">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                    <input type="text" x-model="searchQuery" 
                           placeholder="Cari nama atau email" 
                           class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200/90 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
                </div>
            </div>
        </div>

        <!-- Tabel Data Peminjaman Terbaru Sesuai Mockup -->
        <div class="overflow-x-auto rounded-xl sm:rounded-2xl border border-gray-100 mt-5">
            <table class="w-full text-xs sm:text-sm text-left">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-[#E2E8F0]/40 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-3.5 text-center w-12">No</th>
                        <th scope="col" class="px-4 py-3.5">Nama Peminjam</th>
                        <th scope="col" class="px-4 py-3.5">Fasilitas</th>
                        <th scope="col" class="px-4 py-3.5">Tanggal</th>
                        <th scope="col" class="px-4 py-3.5">Keperluan</th>
                        <th scope="col" class="px-4 py-3.5 text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="(item, idx) in filteredPeminjaman" :key="item.no">
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3.5 text-center font-medium text-gray-600" x-text="item.no"></td>
                            <td class="px-4 py-3.5 font-bold text-gray-900" x-text="item.nama"></td>
                            <td class="px-4 py-3.5 font-medium text-gray-800" x-text="item.fasilitas"></td>
                            <td class="px-4 py-3.5 text-gray-600" x-text="item.tanggal"></td>
                            <td class="px-4 py-3.5 text-gray-700" x-text="item.keperluan"></td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold"
                                      :class="item.status_color"
                                      x-text="item.status">
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredPeminjaman.length === 0">
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            Tidak ada data peminjaman yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
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
        
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs" @click="openModalPdf = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sm:p-8 overflow-hidden z-10"
                 @click.stop>
                
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

                <form method="GET" action="{{ route('admin.laporan.cetak') }}" target="_blank" class="space-y-4 pt-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_awal" class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Mulai</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" required value="{{ now()->startOfYear()->format('Y-m-d') }}"
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
function superAdminLaporanHandler() {
    return {
        openModalPdf: false,
        showYearDropdown: false,
        selectedYear: 2026,
        statusFilter: '',
        searchQuery: '',
        peminjamanList: @json($peminjamanTerbaru),
        monthlyChartInstance: null,
        donutChartInstance: null,

        get filteredPeminjaman() {
            return this.peminjamanList.filter(item => {
                const matchStatus = !this.statusFilter || item.status.toLowerCase() === this.statusFilter.toLowerCase();
                const matchSearch = !this.searchQuery || 
                    item.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                    item.fasilitas.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                    item.keperluan.toLowerCase().includes(this.searchQuery.toLowerCase());
                return matchStatus && matchSearch;
            });
        },

        init() {
            this.$nextTick(() => {
                this.initMonthlyChart();
                this.initDonutChart();
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        changeYear(year) {
            this.selectedYear = year;
            this.showYearDropdown = false;
            
            if (!this.monthlyChartInstance) return;

            if (year === 2026) {
                this.monthlyChartInstance.data.datasets[0].data = [6, 5, 8, 7, 12, 17, 20, 6, 6, 5, 9, 9];
                this.monthlyChartInstance.data.datasets[1].data = [3, 4, 5, 3, 3, 6, 9, 9, 4, 3, 3, 2];
            } else if (year === 2025) {
                this.monthlyChartInstance.data.datasets[0].data = [4, 6, 7, 9, 10, 14, 15, 8, 7, 6, 8, 7];
                this.monthlyChartInstance.data.datasets[1].data = [2, 3, 4, 4, 5, 5, 7, 6, 3, 2, 4, 3];
            } else {
                this.monthlyChartInstance.data.datasets[0].data = [5, 4, 6, 8, 9, 11, 13, 7, 5, 4, 6, 5];
                this.monthlyChartInstance.data.datasets[1].data = [3, 2, 3, 4, 4, 4, 6, 5, 3, 2, 3, 2];
            }
            this.monthlyChartInstance.update();
        },

        initMonthlyChart() {
            const canvas = document.getElementById('monthlyChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            this.monthlyChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartBulanan['labels']),
                    datasets: [
                        {
                            label: 'Ruang',
                            data: @json($chartBulanan['ruang']),
                            backgroundColor: '#E11D48',
                            borderRadius: 2,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6
                        },
                        {
                            label: 'Alat',
                            data: @json($chartBulanan['alat']),
                            backgroundColor: '#2563EB',
                            borderRadius: 2,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 20,
                            ticks: {
                                stepSize: 5,
                                font: { size: 11, weight: '600' },
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
                                font: { size: 11, weight: '600' },
                                color: '#111827'
                            },
                            border: {
                                display: true,
                                color: '#111827',
                                width: 1.5
                            }
                        }
                    }
                }
            });
        },

        initDonutChart() {
            const canvas = document.getElementById('donutChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            this.donutChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Ruang (60%)', 'Alat (25%)', 'Lainnya (15%)'],
                    datasets: [
                        {
                            data: [12, 5, 3],
                            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 10,
                            cornerRadius: 8
                        }
                    }
                }
            });
        }
    };
}
</script>
@endsection
