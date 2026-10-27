<div x-data="{ sidebarOpen: false }" 
     @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
     class="flex shrink-0">
    
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"></div>

    <!-- Sidebar Menu (Sesuai Mockup Desain FST) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 lg:w-72 bg-white text-gray-800 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col border-r border-gray-100 shadow-sm">
        
        <!-- Branding Logo & Nama Website -->
        <div class="h-20 flex items-center px-6 border-b border-gray-100/80">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo-fst.png') }}" 
                     alt="Logo FST" 
                     class="w-10 h-10 object-contain drop-shadow-2xs select-none">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-700 leading-tight">Sistem</span>
                    <span class="text-sm sm:text-base font-black text-[#801414] leading-tight tracking-tight">Peminjaman FST</span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu List -->
        <div class="flex-1 overflow-y-auto py-5 px-4 space-y-2.5 custom-sidebar-scrollbar">
            
            @if(auth()->user()->isAdmin())
                <!-- Menu Utama Admin (Sesuai Mockup) -->
                @php
                    $isDashboard = request()->routeIs('dashboard');
                    $isKelolaRuang = request()->routeIs('admin.ruang.*') || (request()->routeIs('admin.fasilitas.*') && request('jenis') === 'ruangan');
                    $isKelolaAlat = request()->routeIs('admin.alat.*') || (request()->routeIs('admin.fasilitas.*') && request('jenis') === 'peralatan');
                    $isPengajuan = request()->routeIs('admin.verifikasi.*');
                    $isPengembalian = request()->routeIs('admin.pengembalian.*');
                    $isRiwayat = request()->routeIs('admin.riwayat.*');
                    $isLaporan = request()->routeIs('admin.laporan.*');
                @endphp

                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isDashboard ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 {{ $isDashboard ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Kelola Ruang -->
                <a href="{{ route('admin.ruang.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isKelolaRuang ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="door-open" class="w-5 h-5 shrink-0 {{ $isKelolaRuang ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Kelola Ruang</span>
                </a>

                <!-- 3. Kelola Alat -->
                <a href="{{ route('admin.alat.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isKelolaAlat ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="monitor" class="w-5 h-5 shrink-0 {{ $isKelolaAlat ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Kelola Alat</span>
                </a>

                <!-- 4. Pengajuan Peminjaman -->
                <a href="{{ route('admin.verifikasi.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isPengajuan ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="file-text" class="w-5 h-5 shrink-0 {{ $isPengajuan ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Pengajuan Peminjaman</span>
                </a>

                <!-- 5. Pengembalian Alat -->
                <a href="{{ route('admin.pengembalian.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isPengembalian ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="package" class="w-5 h-5 shrink-0 {{ $isPengembalian ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Pengembalian Alat</span>
                </a>

                <!-- 6. Riwayat Peminjaman -->
                <a href="{{ route('admin.riwayat.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isRiwayat ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="clock" class="w-5 h-5 shrink-0 {{ $isRiwayat ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Riwayat Peminjaman</span>
                </a>

                <!-- 7. Laporan & Statistik -->
                <a href="{{ route('admin.laporan.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isLaporan ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="trending-up" class="w-5 h-5 shrink-0 {{ $isLaporan ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Laporan & Statistik</span>
                </a>

                <!-- Menu Tambahan Khusus Admin -->
                <div class="pt-3 pb-1">
                    <p class="px-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Aksi Cepat</p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.peminjaman.buat') }}" 
                       class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-gray-200/80 text-[11px] font-bold text-gray-700 hover:bg-gray-50 hover:text-[#801414] transition-colors text-center" title="Input Peminjaman Langsung">
                        <i data-lucide="plus-square" class="w-4 h-4 mb-1 text-[#801414]"></i>
                        <span>Input Alat</span>
                    </a>
                    <a href="{{ route('admin.jadwal.buat') }}" 
                       class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-gray-200/80 text-[11px] font-bold text-gray-700 hover:bg-gray-50 hover:text-[#801414] transition-colors text-center" title="Input Jadwal Ruangan">
                        <i data-lucide="calendar-plus" class="w-4 h-4 mb-1 text-[#801414]"></i>
                        <span>Input Jadwal</span>
                    </a>
                </div>
            @endif

            @if(auth()->user()->isPengguna())
                <!-- Menu Pengguna / Mahasiswa / Dosen (Sesuai Mockup Beranda User) -->
                @php
                    $isBeranda = request()->routeIs('dashboard');
                    $isKatalogRuang = request()->routeIs('katalog.ruang*') || request()->routeIs('peminjaman.create');
                    $isKatalogAlat = request()->routeIs('katalog.alat');
                    $isFeedback = request()->routeIs('ulasan.*') || request()->routeIs('presensi.*');
                    $isStatusPeminjaman = request()->routeIs('peminjaman.status') || (request()->routeIs('peminjaman.index') && !request()->routeIs('peminjaman.create') && !request()->routeIs('peminjaman.riwayat'));
                    $isRiwayatPeminjaman = request()->routeIs('peminjaman.riwayat');
                    $isAnyPeminjaman = $isStatusPeminjaman || $isRiwayatPeminjaman || (request()->routeIs('peminjaman.show') && !request()->routeIs('peminjaman.create'));
                @endphp

                <!-- 1. Beranda -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isBeranda ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="home" class="w-5 h-5 shrink-0 {{ $isBeranda ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Beranda</span>
                </a>

                <!-- 2. Katalog Ruang -->
                <a href="{{ route('katalog.ruang') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isKatalogRuang ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="building" class="w-5 h-5 shrink-0 {{ $isKatalogRuang ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Katalog Ruang</span>
                </a>

                <!-- 3. Katalog Alat -->
                <a href="{{ route('katalog.alat') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isKatalogAlat ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="monitor" class="w-5 h-5 shrink-0 {{ $isKatalogAlat ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Katalog Alat</span>
                </a>

                <!-- 4. Peminjaman Dropdown (Status & Riwayat) -->
                <div x-data="{ openPeminjaman: {{ $isAnyPeminjaman ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" 
                            @click="openPeminjaman = !openPeminjaman"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all cursor-pointer {{ $isAnyPeminjaman ? 'bg-red-50/80 text-[#801414] border border-red-200/70' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                        <div class="flex items-center gap-3.5">
                            <i data-lucide="file-text" class="w-5 h-5 shrink-0 {{ $isAnyPeminjaman ? 'text-[#801414]' : 'text-gray-700' }}"></i>
                            <span>Peminjaman</span>
                        </div>
                        <i data-lucide="chevron-down" 
                           class="w-4 h-4 transition-transform duration-200 {{ $isAnyPeminjaman ? 'text-[#801414]' : 'text-gray-400' }}"
                           :class="{ 'rotate-180': openPeminjaman }"></i>
                    </button>

                    <!-- Sub-Menu Turunan -->
                    <div x-show="openPeminjaman" 
                         x-collapse
                         x-cloak
                         class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-red-100 ml-4 mt-1">
                        <!-- Turunan 1: Status Peminjaman -->
                        <a href="{{ route('peminjaman.status') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ $isStatusPeminjaman ? 'bg-[#801414] text-white shadow-xs' : 'text-gray-600 hover:bg-red-50/50 hover:text-[#801414]' }}">
                            <i data-lucide="clock" class="w-4 h-4 shrink-0 {{ $isStatusPeminjaman ? 'text-white' : 'text-gray-400' }}"></i>
                            <span>Status Peminjaman</span>
                        </a>

                        <!-- Turunan 2: Riwayat Peminjaman -->
                        <a href="{{ route('peminjaman.riwayat') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ $isRiwayatPeminjaman ? 'bg-[#801414] text-white shadow-xs' : 'text-gray-600 hover:bg-red-50/50 hover:text-[#801414]' }}">
                            <i data-lucide="history" class="w-4 h-4 shrink-0 {{ $isRiwayatPeminjaman ? 'text-white' : 'text-gray-400' }}"></i>
                            <span>Riwayat Peminjaman</span>
                        </a>
                    </div>
                </div>

                <!-- 5. Presensi dan Feedback -->
                <a href="{{ route('ulasan.pengguna') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isFeedback ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="edit-3" class="w-5 h-5 shrink-0 {{ $isFeedback ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Presensi dan Feedback</span>
                </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <!-- Menu Super Admin (Sesuai Mockup Desain FST) -->
                @php
                    $isSuperDashboard = request()->routeIs('dashboard');
                    $isKelolaAdmin = request()->routeIs('super_admin.pengguna.*');
                    $isLaporan = request()->routeIs('super_admin.laporan.*') || request()->routeIs('admin.laporan.*');
                    $isAuditLog = request()->routeIs('super_admin.log.*');
                    $isEvaluasi = request()->routeIs('super_admin.ulasan.*');
                    $isPengaturan = request()->routeIs('super_admin.pengaturan.*');
                @endphp

                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isSuperDashboard ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 shrink-0 {{ $isSuperDashboard ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Kelola Admin -->
                <a href="{{ route('super_admin.pengguna.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isKelolaAdmin ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="users" class="w-5 h-5 shrink-0 {{ $isKelolaAdmin ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Kelola Admin</span>
                </a>

                <!-- 3. Laporan & Statistik -->
                <a href="{{ route('super_admin.laporan.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isLaporan ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="trending-up" class="w-5 h-5 shrink-0 {{ $isLaporan ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Laporan & Statistik</span>
                </a>

                <!-- 4. Audit Log -->
                <a href="{{ route('super_admin.log.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isAuditLog ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="file-text" class="w-5 h-5 shrink-0 {{ $isAuditLog ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Audit Log</span>
                </a>

                <!-- 5. Evaluasi Feedback -->
                <a href="{{ route('super_admin.ulasan.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isEvaluasi ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="message-square" class="w-5 h-5 shrink-0 {{ $isEvaluasi ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Evaluasi Feedback</span>
                </a>

                <!-- 6. Pengaturan Website (Sesuai Permintaan User: Jangan Dihapus!) -->
                <a href="{{ route('super_admin.pengaturan.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all {{ $isPengaturan ? 'bg-[#801414] text-white shadow-sm' : 'bg-white text-gray-800 border border-gray-200/70 hover:border-gray-300 hover:bg-gray-50/80 shadow-2xs' }}">
                    <i data-lucide="settings" class="w-5 h-5 shrink-0 {{ $isPengaturan ? 'text-white' : 'text-gray-700' }}"></i>
                    <span>Pengaturan Website</span>
                </a>
            @endif
            
        </div>
    </aside>
</div>

<style>
    .custom-sidebar-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-sidebar-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .custom-sidebar-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
