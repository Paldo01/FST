@extends('layouts.app')

@section('title', 'Manajemen Pengguna - SIMORA FST')
@section('header', 'Kelola Pengguna Sistem')
@section('description', 'Manajemen akun admin pengelola dan pengguna (mahasiswa/dosen) yang terdaftar.')

@section('header_actions')
    <button x-data @click="$dispatch('open-modal')" class="inline-flex items-center gap-2 px-4 py-2 bg-marun-800 hover:bg-marun-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Admin Baru
    </button>
@endsection

@section('content')

{{-- Search Bar --}}
<div class="mb-6">
    <form method="GET" action="{{ route('super_admin.pengguna.index') }}" class="flex gap-3">
        <div class="flex-1 relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, email, atau NIM/NIP..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-marun-300 focus:border-marun-400 outline-none">
        </div>
        <button type="submit" class="px-4 py-2 bg-marun-800 text-white text-sm font-medium rounded-lg hover:bg-marun-700 transition-colors">Cari</button>
        @if(request('cari'))
        <a href="{{ route('super_admin.pengguna.index') }}" class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1 shadow-sm">
            <i data-lucide="x" class="w-4 h-4"></i> Reset
        </a>
        @endif
    </form>
</div>

{{-- ====================================================== --}}
{{-- TABEL 1: ADMIN (PENGELOLA) --}}
{{-- ====================================================== --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-8">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-mustard-100 flex items-center justify-center">
                <i data-lucide="shield-check" class="w-4 h-4 text-mustard-700"></i>
            </span>
            <div>
                <h2 class="text-base font-bold text-gray-900">Admin / Pengelola Fasilitas</h2>
                <p class="text-xs text-gray-500">{{ $adminList->total() }} akun admin terdaftar</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Nama & Kontak</th>
                    <th class="px-6 py-3">NIP</th>
                    <th class="px-6 py-3">Bergabung</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($adminList as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-mustard-100 text-mustard-800 flex items-center justify-center font-bold text-sm uppercase shrink-0">
                                {{ substr($p->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-gray-700 text-xs">{{ $p->nomor_identitas }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('super_admin.pengguna.destroy', $p->id) }}" method="POST" class="inline"
                              x-data @submit.prevent="if(confirm('Hapus akun admin {{ $p->nama_lengkap }}? Aksi ini tidak dapat dibatalkan.')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                        <p>Belum ada akun admin yang terdaftar.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($adminList->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $adminList->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ====================================================== --}}
{{-- TABEL 2: MAHASISWA --}}
{{-- ====================================================== --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-8">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                <i data-lucide="graduation-cap" class="w-4 h-4 text-blue-600"></i>
            </span>
            <div>
                <h2 class="text-base font-bold text-gray-900">Mahasiswa</h2>
                <p class="text-xs text-gray-500">{{ $mahasiswaList->total() }} akun mahasiswa terdaftar</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Nama & Kontak</th>
                    <th class="px-6 py-3">NIM</th>
                    <th class="px-6 py-3">No. Telepon</th>
                    <th class="px-6 py-3">Bergabung</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($mahasiswaList as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm uppercase shrink-0">
                                {{ substr($p->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-gray-700 text-xs">{{ $p->nomor_identitas }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->nomor_telepon ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('super_admin.pengguna.destroy', $p->id) }}" method="POST" class="inline"
                              x-data @submit.prevent="if(confirm('Hapus akun mahasiswa {{ $p->nama_lengkap }}? Semua data peminjaman miliknya juga akan terhapus.')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                        <p>Belum ada mahasiswa yang terdaftar.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mahasiswaList->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $mahasiswaList->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ====================================================== --}}
{{-- TABEL 3: DOSEN MENUNGGU KONFIRMASI --}}
{{-- ====================================================== --}}
@if($dosenMenungguList->count() > 0)
<div class="bg-white rounded-xl border border-yellow-200 shadow-sm mb-8 relative overflow-hidden">
    <div class="absolute top-0 left-0 w-1 h-full bg-yellow-400"></div>
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                <i data-lucide="clock" class="w-4 h-4 text-yellow-600"></i>
            </span>
            <div>
                <h2 class="text-base font-bold text-gray-900">Dosen Menunggu Konfirmasi</h2>
                <p class="text-xs text-gray-500">{{ $dosenMenungguList->total() }} akun perlu persetujuan Anda</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Nama & Kontak</th>
                    <th class="px-6 py-3">NIP</th>
                    <th class="px-6 py-3">No. Telepon</th>
                    <th class="px-6 py-3">Mendaftar Pada</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($dosenMenungguList as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-sm uppercase shrink-0">
                                {{ substr($p->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-gray-700 text-xs">{{ $p->nomor_identitas }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->nomor_telepon ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('super_admin.pengguna.setujui', $p->id) }}" method="POST" class="inline"
                                  x-data @submit.prevent="if(confirm('Setujui pendaftaran dosen {{ $p->nama_lengkap }}?')) $el.submit()">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded-lg transition-colors text-xs font-medium flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('super_admin.pengguna.destroy', $p->id) }}" method="POST" class="inline"
                                  x-data @submit.prevent="if(confirm('Tolak dan hapus pendaftaran dosen {{ $p->nama_lengkap }}?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors text-xs font-medium flex items-center gap-1">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($dosenMenungguList->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $dosenMenungguList->withQueryString()->links() }}
    </div>
    @endif
</div>
@endif

{{-- ====================================================== --}}
{{-- TABEL 4: DOSEN --}}
{{-- ====================================================== --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i data-lucide="book-open" class="w-4 h-4 text-emerald-600"></i>
            </span>
            <div>
                <h2 class="text-base font-bold text-gray-900">Dosen</h2>
                <p class="text-xs text-gray-500">{{ $dosenList->total() }} akun dosen terdaftar</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Nama & Kontak</th>
                    <th class="px-6 py-3">NIP</th>
                    <th class="px-6 py-3">No. Telepon</th>
                    <th class="px-6 py-3">Bergabung</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($dosenList as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm uppercase shrink-0">
                                {{ substr($p->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-gray-700 text-xs">{{ $p->nomor_identitas }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->nomor_telepon ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('super_admin.pengguna.destroy', $p->id) }}" method="POST" class="inline"
                              x-data @submit.prevent="if(confirm('Hapus akun dosen {{ $p->nama_lengkap }}? Semua data peminjaman miliknya juga akan terhapus.')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                        <p>Belum ada dosen yang terdaftar.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($dosenList->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $dosenList->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Modal Tambah Admin --}}
<div x-data="{ open: false }"
    x-on:open-modal.window="open = true"
    class="relative z-50">

    <div x-show="open" style="display:none;" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" x-transition.opacity></div>

    <div x-show="open" style="display:none;" class="fixed inset-0 z-10 overflow-y-auto" x-transition>
        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.away="open = false" class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                {{-- Gradient Line --}}
                <div class="h-1.5 w-full" style="background: linear-gradient(to right, #800000, #E5A93C);"></div>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-marun-50 flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-5 h-5 text-marun-800"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Tambah Pengelola (Admin)</h3>
                    </div>
                    <form action="{{ route('super_admin.pengguna.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" required placeholder="Contoh: Dr. Ahmad Fauzi"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-mustard-400 focus:border-mustard-400 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                                <input type="text" name="nomor_identitas" required placeholder="198XXXXXXXXXX"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-mustard-400 focus:border-mustard-400 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                                <input type="text" name="nomor_telepon" placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-mustard-400 focus:border-mustard-400 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required placeholder="nama@fst-uinib.ac.id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-mustard-400 focus:border-mustard-400 outline-none">
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="kata_sandi" required minlength="8"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-mustard-400 focus:border-mustard-400 outline-none">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
                                    <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                            <button type="button" @click="open = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-marun-800 text-white rounded-lg text-sm hover:bg-marun-700 font-medium transition-colors">Buat Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ====================================================== --}}
{{-- TABEL: PENGGUNA LAMA (Belum Ada Jenis) --}}
{{-- ====================================================== --}}
@if($lainnyaList->count() > 0)
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-8">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
        <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-gray-500"></i>
        </span>
        <div>
            <h2 class="text-base font-bold text-gray-700">Pengguna Lama (Tanpa Jenis)</h2>
            <p class="text-xs text-gray-400">{{ $lainnyaList->total() }} akun tanpa kategori — didaftarkan sebelum fitur ini ada. Silakan hapus atau minta mereka daftar ulang.</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Nama & Kontak</th>
                    <th class="px-6 py-3">NIM / NIP</th>
                    <th class="px-6 py-3">Bergabung</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($lainnyaList as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-bold text-sm uppercase shrink-0">
                                {{ substr($p->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-gray-700 text-xs">{{ $p->nomor_identitas }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('super_admin.pengguna.destroy', $p->id) }}" method="POST" class="inline"
                              x-data @submit.prevent="if(confirm('Hapus akun {{ $p->nama_lengkap }}?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($lainnyaList->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $lainnyaList->withQueryString()->links() }}
    </div>
    @endif
</div>
@endif

@endsection
