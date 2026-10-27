@extends('layouts.app')

@section('title', 'Kategori Fasilitas')
@section('header', 'Kelola Kategori')
@section('description', 'Manajemen kategori untuk ruangan dan peralatan.')

@section('header_actions')
    <button x-data @click="$dispatch('open-modal', 'modal-tambah')" class="inline-flex items-center gap-2 px-4 py-2 bg-marun-800 hover:bg-marun-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
    </button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-bold text-gray-900">Kategori Ruangan</h3>
        </x-slot>
        
        <div class="space-y-3">
            @foreach($kategori->where('jenis', 'ruangan') as $k)
                <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-lg hover:bg-white hover:shadow-sm transition-all group">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $k->nama_kategori }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $k->fasilitas_count }} Ruangan</p>
                    </div>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button x-data @click="$dispatch('open-edit-modal', { id: {{ $k->id }}, nama: '{{ $k->nama_kategori }}', jenis: '{{ $k->jenis }}', deskripsi: '{{ $k->deskripsi }}' })" 
                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>
                        @if($k->fasilitas_count === 0)
                            <form action="{{ route('admin.kategori.destroy', $k->id) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Hapus kategori?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>

    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-bold text-gray-900">Kategori Peralatan</h3>
        </x-slot>
        
        <div class="space-y-3">
            @foreach($kategori->where('jenis', 'peralatan') as $k)
                <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-lg hover:bg-white hover:shadow-sm transition-all group">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $k->nama_kategori }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $k->fasilitas_count }} Alat</p>
                    </div>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button x-data @click="$dispatch('open-edit-modal', { id: {{ $k->id }}, nama: '{{ $k->nama_kategori }}', jenis: '{{ $k->jenis }}', deskripsi: '{{ $k->deskripsi }}' })" 
                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>
                        @if($k->fasilitas_count === 0)
                            <form action="{{ route('admin.kategori.destroy', $k->id) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Hapus kategori?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
</div>

<!-- Modal Tambah/Edit (AlpineJS) -->
<div x-data="{ 
        open: false, 
        mode: 'add',
        form: { id: '', nama: '', jenis: 'ruangan', deskripsi: '' },
        openModal() { this.mode = 'add'; this.form = { id: '', nama: '', jenis: 'ruangan', deskripsi: '' }; this.open = true; },
        openEdit(data) { this.mode = 'edit'; this.form = data; this.open = true; },
        get action() { return this.mode === 'add' ? '{{ route('admin.kategori.store') }}' : '/admin/kategori/' + this.form.id; }
    }" 
    @open-modal.window="openModal()"
    @open-edit-modal.window="openEdit($event.detail)"
    class="relative z-50">
    
    <div x-show="open" style="display: none;" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" x-transition.opacity></div>

    <div x-show="open" style="display: none;" class="fixed inset-0 z-10 overflow-y-auto" x-transition>
        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.away="open = false" class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4" x-text="mode === 'add' ? 'Tambah Kategori Baru' : 'Edit Kategori'"></h3>
                
                <form :action="action" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" name="nama_kategori" required x-model="form.nama"
                            class="w-full rounded-lg border-gray-300 focus:ring-mustard-400 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                        <select name="jenis" x-model="form.jenis" class="w-full rounded-lg border-gray-300 focus:ring-mustard-400 text-sm">
                            <option value="ruangan">Ruangan</option>
                            <option value="peralatan">Peralatan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" x-model="form.deskripsi" rows="2" class="w-full rounded-lg border-gray-300 focus:ring-mustard-400 text-sm"></textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-marun-800 text-white rounded-lg text-sm hover:bg-marun-700 font-medium">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
