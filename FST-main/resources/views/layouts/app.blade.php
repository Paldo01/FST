<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIMORA FST UINIB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('styles')
</head>
<body class="bg-off-white text-slate-text font-sans antialiased flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Navbar -->
        @include('components.navbar')

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                @hasSection('header')
                    <div class="mb-6 flex justify-between items-end animate-fade-in-up">
                        <div>
                            <h1 class="text-2xl font-bold text-marun-900">@yield('header')</h1>
                            <p class="text-sm text-gray-500 mt-1">@yield('description', 'Sistem Manajemen Peminjaman Ruang dan Alat')</p>
                        </div>
                        @yield('header_actions')
                    </div>
                @endif

                <!-- Flash Messages via Toast -->
                <x-toast />

                <!-- Main Content -->
                <div class="animate-fade-in-up" style="animation-delay: 0.1s;">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
