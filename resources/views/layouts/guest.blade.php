<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $appNamaWebsite ?? 'SIMORA FST UINIB')</title>
    <link rel="icon" type="image/png" href="{{ $appFavicon ?? asset('images/logo-fst.png') }}">
    <meta name="description" content="{{ $appDeskripsi ?? 'Sistem Manajemen Peminjaman Ruang dan Alat' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('styles')
</head>
<body class="@yield('body_class', 'bg-off-white text-slate-text font-sans antialiased min-h-screen flex flex-col')">
    
    @unless(View::hasSection('no_nav'))
    <!-- Navbar untuk Publik -->
    <nav class="w-full bg-white shadow-sm border-b border-gray-100 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('beranda') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-lg overflow-hidden flex items-center justify-center">
                            <img src="{{ $appLogo ?? asset('images/logo-fst.png') }}" alt="{{ $appNamaWebsite ?? 'Logo' }}" class="max-w-full max-h-full object-contain">
                        </div>
                        <span class="font-bold text-xl tracking-tight" style="color: {{ $appWarnaNamaWebsite ?? '#801414' }};">{{ $appNamaWebsite ?? 'SIMORA FST' }}</span>
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-marun-800 hover:text-mustard-600 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('masuk') }}" class="text-sm font-medium text-gray-600 hover:text-marun-800 transition-colors">Masuk</a>
                        <a href="{{ route('daftar') }}" class="text-sm font-medium px-4 py-2 bg-marun-800 text-white rounded-md hover:bg-marun-700 transition-colors">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    @endunless

    <!-- Main Content Area -->
    <main class="@yield('main_class', 'flex-1 flex flex-col items-center justify-center')">
        <!-- Flash Messages via Toast -->
        <x-toast />
        
        @yield('content')
    </main>

    @unless(View::hasSection('no_footer'))
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} Fakultas Sains dan Teknologi, UIN Imam Bonjol Padang.
            </p>
            <div class="flex gap-4 text-gray-400">
                <a href="#" class="hover:text-marun-700 transition-colors"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                <a href="#" class="hover:text-marun-700 transition-colors"><i data-lucide="twitter" class="w-5 h-5"></i></a>
                <a href="#" class="hover:text-marun-700 transition-colors"><i data-lucide="instagram" class="w-5 h-5"></i></a>
            </div>
        </div>
    </footer>
    @endunless

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
