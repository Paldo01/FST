<div x-data="{ sidebarOpen: false }" 
     @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
     class="flex shrink-0">
    
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Sidebar Menu -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-marun-900 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col border-r border-marun-950 shadow-xl">
        
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 bg-marun-950 border-b border-marun-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 bg-mustard-500 rounded flex items-center justify-center text-marun-950">
                    <i data-lucide="building-2" class="w-5 h-5"></i>
                </div>
                <span class="font-bold text-lg tracking-tight">SIMORA <span class="text-mustard-400">FST</span></span>
            </a>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Dashboard
            </a>

            @if(auth()->user()->isPengguna())
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-marun-300 uppercase tracking-wider">Menu</p>
                </div>
                <a href="{{ route('beranda') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('beranda') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    Beranda
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-marun-300 uppercase tracking-wider">Katalog Fasilitas</p>
                </div>
                <a href="{{ route('katalog.ruang') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('katalog.ruang') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="door-open" class="w-5 h-5"></i>
                    Katalog Ruang
                </a>
                <a href="{{ route('katalog.alat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('katalog.alat') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="cpu" class="w-5 h-5"></i>
                    Katalog Alat
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-marun-300 uppercase tracking-wider">Peminjaman</p>
                </div>
                <a href="{{ route('peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('peminjaman.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                    Riwayat Peminjaman
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-marun-300 uppercase tracking-wider">Kelola Fasilitas</p>
                </div>

                <a href="{{ route('admin.peminjaman.buat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.peminjaman.buat') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="plus-square" class="w-5 h-5"></i>
                    Input Peminjaman Alat
                </a>
                
                <a href="{{ route('admin.verifikasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.verifikasi.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="inbox" class="w-5 h-5"></i>
                    Verifikasi Peminjaman
                </a>
                
                <a href="{{ route('admin.pengembalian.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.pengembalian.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    Pengembalian Alat
                </a>

                <a href="{{ route('admin.fasilitas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.fasilitas.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="box" class="w-5 h-5"></i>
                    Data Fasilitas
                </a>

                <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.kategori.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="tags" class="w-5 h-5"></i>
                    Kategori
                </a>

                <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.laporan.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    Laporan PDF
                </a>

                <a href="{{ route('admin.riwayat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.riwayat.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    Riwayat Peminjaman
                </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-marun-300 uppercase tracking-wider">Super Admin</p>
                </div>
                
                <a href="{{ route('super_admin.pengguna.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('super_admin.pengguna.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    Kelola Admin
                </a>

                <a href="{{ route('super_admin.ulasan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('super_admin.ulasan.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="star" class="w-5 h-5"></i>
                    Ulasan Layanan
                </a>

                <a href="{{ route('super_admin.log.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('super_admin.log.*') ? 'bg-marun-800 text-mustard-400' : 'text-gray-300 hover:bg-marun-800/50 hover:text-white' }}">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                    Log Aktivitas
                </a>
            @endif
            
        </div>
        
        <!-- User quick action at bottom -->
        <div class="p-4 bg-marun-950 border-t border-marun-800">
            <form method="POST" action="{{ route('keluar') }}">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-marun-800 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>
</div>

<style>
    /* Custom Scrollbar for sidebar to make it dark themed */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6b1a1a; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #e5a93c; }
</style>
