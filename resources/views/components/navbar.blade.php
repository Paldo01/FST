<header class="bg-white border-b border-gray-100 h-18 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 gap-4">
    <div class="flex items-center gap-3">
        <!-- Mobile Sidebar Toggle -->
        <button x-data @click="$dispatch('toggle-sidebar')" class="lg:hidden text-gray-500 hover:text-[#801414] focus:outline-none p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
    </div>

    <div class="flex items-center gap-3 sm:gap-4 shrink-0">
        @php
            $notifikasiTerbaru = auth()->user()->notifications()->latest()->take(5)->get();
            $jumlahBelumDibaca = auth()->user()->unreadNotifications()->count();
        @endphp
        <div x-data="{ notifikasiTerbuka: false }" class="relative">
            <button type="button" @click="notifikasiTerbuka = !notifikasiTerbuka" :aria-expanded="notifikasiTerbuka"
                    class="relative p-2 text-gray-600 hover:text-[#801414] transition-colors rounded-full hover:bg-gray-50 cursor-pointer"
                    title="Notifikasi" aria-label="Notifikasi">
                <i data-lucide="bell" class="w-5 h-5"></i>
                @if ($jumlahBelumDibaca > 0)
                    <span class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 bg-[#801414] text-white text-[10px] leading-4 text-center rounded-full">
                        {{ $jumlahBelumDibaca > 9 ? '9+' : $jumlahBelumDibaca }}
                    </span>
                @endif
            </button>

            <div x-cloak x-show="notifikasiTerbuka" @click.away="notifikasiTerbuka = false"
                 class="absolute right-0 mt-3 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <p class="font-bold text-sm text-gray-900">Notifikasi</p>
                    @if ($jumlahBelumDibaca > 0)
                        <form method="POST" action="{{ route('notifikasi.baca_semua') }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-[#801414] hover:underline">Tandai semua dibaca</button>
                        </form>
                    @endif
                </div>

                @forelse ($notifikasiTerbaru as $notifikasi)
                    <form method="POST" action="{{ route('notifikasi.baca', $notifikasi->id) }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $notifikasi->read_at ? '' : 'bg-red-50/50' }}">
                            <span class="block text-sm font-semibold text-gray-900">{{ $notifikasi->data['title'] ?? 'Notifikasi baru' }}</span>
                            <span class="block mt-1 text-xs text-gray-600">{{ $notifikasi->data['message'] ?? '' }}</span>
                            <span class="block mt-1.5 text-[11px] text-gray-400">{{ $notifikasi->created_at->diffForHumans() }}</span>
                        </button>
                    </form>
                @empty
                    <p class="px-4 py-6 text-sm text-center text-gray-500">Belum ada notifikasi.</p>
                @endforelse
            </div>
        </div>

        <!-- User Dropdown (Sesuai Mockup) -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" 
                    class="flex items-center gap-2 hover:bg-gray-50 p-1.5 sm:px-3 sm:py-2 rounded-xl sm:rounded-2xl border border-transparent hover:border-gray-200 transition-all focus:outline-none cursor-pointer">
                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center font-bold text-sm border border-gray-200">
                    <i data-lucide="user" class="w-4 h-4"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs sm:text-sm font-bold text-gray-900 leading-tight">
                        {{ auth()->user()->isAdmin() ? 'Admin FST' : auth()->user()->nama_lengkap }}
                    </p>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50"
                 style="display: none;">
                
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->nama_lengkap }}</p>
                    <p class="text-[11px] text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->peran) }}</p>
                </div>

                <a href="{{ route('profil.index') }}" class="block px-4 py-2.5 text-xs sm:text-sm text-gray-700 hover:bg-[#801414]/5 hover:text-[#801414] flex items-center gap-2.5 transition-colors">
                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i> Profil Saya
                </a>

                <a href="{{ route('beranda') }}" class="block px-4 py-2.5 text-xs sm:text-sm text-gray-700 hover:bg-[#801414]/5 hover:text-[#801414] flex items-center gap-2.5 transition-colors">
                    <i data-lucide="home" class="w-4 h-4 text-gray-400"></i> Beranda Publik
                </a>
                
                <div class="border-t border-gray-100 my-1"></div>
                
                <form method="POST" action="{{ route('keluar') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2.5 text-xs sm:text-sm text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
