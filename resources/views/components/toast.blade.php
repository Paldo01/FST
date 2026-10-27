@if (session('pesan_sukses') || session('pesan_error') || $errors->any())
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full">
         
        @if (session('pesan_sukses'))
            <div class="bg-white border-l-4 border-green-500 rounded shadow-lg p-4 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-green-500 bg-green-100 p-1.5 rounded-full">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Sukses</p>
                        <p class="text-sm text-gray-500">{{ session('pesan_sukses') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if (session('pesan_error'))
            <div class="bg-white border-l-4 border-red-500 rounded shadow-lg p-4 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-red-500 bg-red-100 p-1.5 rounded-full">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Error</p>
                        <p class="text-sm text-gray-500">{{ session('pesan_error') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-white border-l-4 border-red-500 rounded shadow-lg p-4 flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="text-red-500 bg-red-100 p-1.5 rounded-full shrink-0 mt-0.5">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Terdapat Kesalahan</p>
                        <ul class="text-sm text-red-600 mt-1 list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>
@endif
