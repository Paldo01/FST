@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem')
@section('header', 'Audit Trail (Log Aktivitas)')
@section('description', 'Rekaman aktivitas penting yang dilakukan oleh pengguna dalam sistem.')

@section('content')
<x-card>
    <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-100 text-marun-800">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-sm">Sistem Audit Aktif</h4>
                <p class="text-xs text-gray-500">Perekaman otomatis untuk setiap CRUD operasi.</p>
            </div>
        </div>
        
        <form method="GET" action="{{ route('super_admin.log.index') }}" class="flex gap-2 w-full sm:w-auto" x-data>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                class="text-sm border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 flex-1 sm:w-40" @change="$el.form.submit()">
            <a href="{{ route('super_admin.log.index') }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium">
                Reset
            </a>
        </form>
    </div>

    <div class="relative max-w-4xl mx-auto">
        <!-- Timeline Line -->
        <div class="absolute left-4 md:left-24 top-0 bottom-0 w-0.5 bg-gray-200"></div>

        <div class="space-y-6 relative">
            @forelse($logs as $log)
                <div class="flex flex-col md:flex-row gap-4 md:gap-8 relative">
                    <!-- Time (Desktop) -->
                    <div class="hidden md:block w-16 pt-1 text-right shrink-0">
                        <span class="text-xs font-bold text-gray-500">{{ $log->created_at->format('H:i') }}</span>
                    </div>

                    <!-- Icon / Bullet -->
                    <div class="absolute left-4 md:left-24 -translate-x-1/2 mt-1.5 w-3 h-3 rounded-full border-2 border-white 
                        {{ str_contains(strtolower($log->aksi), 'tambah') || str_contains(strtolower($log->aksi), 'buat') ? 'bg-green-500' : 
                           (str_contains(strtolower($log->aksi), 'hapus') || str_contains(strtolower($log->aksi), 'tolak') ? 'bg-red-500' : 
                           (str_contains(strtolower($log->aksi), 'ubah') || str_contains(strtolower($log->aksi), 'edit') ? 'bg-blue-500' : 'bg-gray-400')) }}">
                    </div>

                    <!-- Content Card -->
                    <div class="ml-10 md:ml-0 flex-1 bg-white border border-gray-100 shadow-sm rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-sm">{{ $log->pengguna->nama_lengkap }}</span>
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600 uppercase">{{ $log->pengguna->peran }}</span>
                            </div>
                            <span class="text-xs text-gray-400 md:hidden">{{ $log->created_at->format('H:i') }}</span>
                        </div>
                        
                        <p class="text-sm text-gray-700"><span class="font-medium text-marun-800">{{ $log->aksi }}</span>: {{ $log->keterangan }}</p>
                        
                        <div class="mt-2 text-xs text-gray-400 flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i> {{ $log->created_at->format('d M Y') }} ({{ $log->created_at->diffForHumans() }})
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300 ml-10 md:ml-24">
                    <p class="text-gray-500">Tidak ada log aktivitas pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($logs->hasPages())
        <div class="mt-8 border-t border-gray-100 pt-4 pl-10 md:pl-32">
            {{ $logs->withQueryString()->links() }}
        </div>
    @endif
</x-card>
@endsection
