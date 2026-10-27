<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button x-data @click="$dispatch('toggle-sidebar')" class="lg:hidden text-gray-500 hover:text-marun-800 focus:outline-none">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        
        <!-- Search (Optional placeholder) -->
        <div class="hidden sm:block relative text-gray-400 focus-within:text-marun-800">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4"></i>
            <input type="text" placeholder="Cari..." class="bg-gray-50 border-none rounded-full py-1.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-mustard-400 focus:bg-white w-64 transition-all">
        </div>
    </div>

    <div class="flex items-center gap-4">
        <!-- Notifications -->
        <button class="relative p-2 text-gray-400 hover:text-marun-800 transition-colors rounded-full hover:bg-gray-50">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-mustard-500 rounded-full animate-pulse"></span>
        </button>

        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 hover:bg-gray-50 p-1.5 rounded-lg transition-colors focus:outline-none">
                <div class="w-8 h-8 rounded-full bg-marun-100 text-marun-800 flex items-center justify-center font-bold text-sm border border-marun-200">
                    {{ substr(auth()->user()->nama_lengkap, 0, 1) }}
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-gray-700 leading-tight">{{ auth()->user()->nama_lengkap }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->peran) }}</p>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-100 py-1 z-50 hidden"
                 :class="{'hidden': !open}">
                
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-marun-50 hover:text-marun-800 flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                </a>
                <a href="{{ route('beranda') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-marun-50 hover:text-marun-800 flex items-center gap-2">
                    <i data-lucide="home" class="w-4 h-4"></i> Beranda Publik
                </a>
                
                <div class="border-t border-gray-100 my-1"></div>
                
                <form method="POST" action="{{ route('keluar') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
