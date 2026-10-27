@extends('layouts.app')

@section('title', 'Manajemen Fasilitas')
@section('header', 'Data Fasilitas')
@section('description', 'Kelola semua ruangan dan peralatan di fakultas.')

@section('header_actions')
    <a href="{{ route('admin.fasilitas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-marun-800 hover:bg-marun-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Fasilitas
    </a>
@endsection

@section('content')
<x-card>
    <!-- Filters -->
    <form method="GET" action="{{ route('admin.fasilitas.index') }}" class="flex flex-col sm:flex-row gap-4 mb-6" x-data>
        <div class="flex-1 relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau kode..." 
                class="w-full pl-9 pr-4 py-2 text-sm border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400">
        </div>
        
        <select name="jenis" class="text-sm border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 w-full sm:w-48" @change="$el.form.submit()">
            <option value="">Semua Jenis</option>
            <option value="ruangan" {{ request('jenis') == 'ruangan' ? 'selected' : '' }}>Ruangan</option>
            <option value="peralatan" {{ request('jenis') == 'peralatan' ? 'selected' : '' }}>Peralatan</option>
        </select>
        
        <select name="status" class="text-sm border-gray-300 rounded-lg focus:ring-mustard-400 focus:border-mustard-400 w-full sm:w-48" @change="$el.form.submit()">
            <option value="">Semua Status</option>
            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
            <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            <option value="perbaikan" {{ request('status') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
        </select>
        
        <a href="{{ route('admin.fasilitas.index') }}" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg whitespace-nowrap transition-colors flex items-center justify-center gap-2">
            <i data-lucide="x" class="w-4 h-4"></i> Reset
        </a>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-4">Kode</th>
                    <th scope="col" class="px-6 py-4">Fasilitas</th>
                    <th scope="col" class="px-6 py-4">Kategori</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fasilitas as $f)
                    <tr class="bg-white border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $f->kode_fasilitas }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($f->gambar)
                                    <img src="{{ Storage::url($f->gambar) }}" class="w-10 h-10 rounded object-cover">
                                @else
                                    <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                                        <i data-lucide="image" class="w-5 h-5"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900">{{ $f->nama_fasilitas }}</p>
                                    <p class="text-xs text-gray-500 max-w-[200px] truncate" title="{{ $f->deskripsi }}">{{ Str::limit($f->deskripsi, 40) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ $f->kategori->nama_kategori }}
                            <span class="block text-xs text-gray-400 capitalize">{{ $f->kategori->jenis }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :status="$f->status" />
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.fasilitas.edit', $f->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.fasilitas.destroy', $f->id) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Yakin ingin menghapus fasilitas ini?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data fasilitas ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fasilitas->hasPages())
        <div class="mt-6 border-t border-gray-100 pt-4">
            {{ $fasilitas->withQueryString()->links() }}
        </div>
    @endif
</x-card>
@endsection
