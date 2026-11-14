@extends('layouts.app')

@section('title', 'Kelola Ruang - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12" x-data="kelolaRuangHandler()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Kelola Ruang
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Kelola data ruang dan ketersediaan fasilitas FST
        </p>
    </div>

    <!-- Filter Bar & Tombol Tambah Ruang (Sesuai Mockup) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3.5">
        
        <!-- Form Filter (Pencarian & Jenis) -->
        <form method="GET" action="{{ route('admin.ruang.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            
            <!-- Pencarian Nama Ruang -->
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input type="text" name="cari" value="{{ request('cari') }}" 
                       placeholder="Cari nama ruang" 
                       class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
            </div>

            <!-- Dropdown Filter Jenis Ruangan -->
            <div class="relative sm:w-56 shrink-0">
                <select name="kategori_id" onchange="this.form.submit()"
                        class="w-full appearance-none px-4 py-2.5 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer pr-10">
                    <option value="">Semua Jenis</option>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </div>

            @if(request()->hasAny(['cari', 'kategori_id']))
                <a href="{{ route('admin.ruang.index') }}" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs sm:text-sm font-semibold transition-colors flex items-center justify-center gap-1.5 shrink-0" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>

        <!-- Tombol Tambah Ruang (Sesuai Mockup: Solid Maroon + Icon) -->
        <button type="button" @click="openAddModal()"
                class="px-5 py-2.5 rounded-xl sm:rounded-2xl bg-[#801414] hover:bg-[#681010] active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer select-none">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Ruang</span>
        </button>
    </div>

    <!-- Card Tabel Daftar Ruang (Sesuai Mockup) -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-7">
        
        <!-- Header Tabel -->
        <div class="flex items-center gap-2 mb-5">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                Daftar Ruang
            </h2>
            <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-0.5 rounded-full font-bold">
                {{ $ruangan->total() }} Ruang
            </span>
        </div>

        <!-- Tabel Data Ruangan (Bordered Table Sesuai Mockup) -->
        <div class="overflow-x-auto rounded-2xl border border-gray-200">
            <table class="w-full text-xs sm:text-sm text-left border-collapse">
                <thead class="bg-white border-b border-gray-200 text-gray-900 font-bold">
                    <tr>
                        <th class="py-3.5 px-4 text-center border-r border-gray-200 w-14">No</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Nama Ruang</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Jenis</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Kapasitas</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Status</th>
                        <th class="py-3.5 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-800">
                    @forelse($ruangan as $index => $r)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <!-- No -->
                            <td class="py-3.5 px-4 text-center font-semibold text-gray-600 border-r border-gray-200">
                                {{ $loop->iteration + ($ruangan->currentPage() - 1) * $ruangan->perPage() }}
                            </td>
                            
                            <!-- Nama Ruang -->
                            <td class="py-3.5 px-5 font-bold text-gray-900 border-r border-gray-200 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($r->gambar && file_exists(public_path($r->gambar)))
                                            <img src="{{ asset($r->gambar) }}" alt="{{ $r->nama_fasilitas }}" class="w-full h-full object-cover">
                                        @elseif($r->gambar && file_exists(public_path('storage/' . $r->gambar)))
                                            <img src="{{ asset('storage/' . $r->gambar) }}" alt="{{ $r->nama_fasilitas }}" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="door-open" class="w-5 h-5 text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 leading-snug">{{ $r->nama_fasilitas }}</p>
                                        <p class="text-[11px] font-mono text-gray-400 font-normal">{{ $r->kode_fasilitas }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Jenis -->
                            <td class="py-3.5 px-5 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">
                                {{ $r->kategori->nama_kategori ?? '-' }}
                            </td>

                            <!-- Kapasitas -->
                            <td class="py-3.5 px-5 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">
                                {{ $r->kapasitas_formatted }}
                            </td>

                            <!-- Status (Badge Bulat Halus Sesuai Mockup) -->
                            <td class="py-3.5 px-5 border-r border-gray-200 whitespace-nowrap">
                                @if($r->status === 'tersedia')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tersedia
                                    </span>
                                @elseif($r->status === 'dipinjam')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Perbaikan
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi (Tombol Edit dan Hapus Sesuai Mockup) -->
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-2">
                                    @php
                                        $gambarUrl = null;
                                        if ($r->gambar && file_exists(public_path($r->gambar))) {
                                            $gambarUrl = asset($r->gambar);
                                        } elseif ($r->gambar && file_exists(public_path('storage/' . $r->gambar))) {
                                            $gambarUrl = asset('storage/' . $r->gambar);
                                        }

                                        $galeriUrls = [];
                                        if (is_array($r->galeri)) {
                                            foreach ($r->galeri as $idx => $gPath) {
                                                if ($gPath && file_exists(public_path($gPath))) {
                                                    $galeriUrls[$idx] = asset($gPath);
                                                } elseif ($gPath && file_exists(public_path('storage/' . $gPath))) {
                                                    $galeriUrls[$idx] = asset('storage/' . $gPath);
                                                }
                                            }
                                        }

                                        $roomPayload = array_merge($r->toArray(), [
                                            'gambar_url' => $gambarUrl,
                                            'galeri_urls' => $galeriUrls,
                                        ]);
                                    @endphp
                                    <!-- Tombol Edit (Outlined Red) -->
                                    <button type="button" @click="openEditModal({{ json_encode($roomPayload) }})"
                                            class="px-3 py-1.5 rounded-lg border border-rose-300 text-[#801414] hover:bg-rose-50 active:scale-95 text-xs font-bold transition-all inline-flex items-center gap-1 cursor-pointer">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Tombol Hapus (Outlined Gray) -->
                                    <form method="POST" action="{{ route('admin.fasilitas.destroy', $r->id) }}" 
                                          class="inline-block"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan {{ addslashes($r->nama_fasilitas) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 active:scale-95 text-xs font-bold transition-all inline-flex items-center gap-1 cursor-pointer">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-gray-500"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-500">
                                <i data-lucide="door-closed" class="w-10 h-10 mx-auto mb-2 text-gray-400"></i>
                                <p class="font-bold text-gray-800 text-sm">Tidak Ada Data Ruang Ditemukan</p>
                                <p class="text-xs text-gray-400 mt-0.5">Silakan tambahkan ruangan baru atau sesuaikan filter pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($ruangan->hasPages())
            <div class="mt-5 pt-3 border-t border-gray-100">
                {{ $ruangan->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah / Edit Ruang (Interactive Modal) -->
    <div x-show="isModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="isModalOpen = false" 
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-gray-100 relative transition-all">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#801414]/10 text-[#801414] flex items-center justify-center">
                        <i data-lucide="door-open" class="w-5 h-5"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base sm:text-lg" x-text="modalMode === 'add' ? 'Tambah Ruangan Baru' : 'Edit Data Ruangan'"></h3>
                </div>
                <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Ruang -->
            <form :action="modalMode === 'add' ? '{{ route('admin.fasilitas.store') }}' : '{{ url('admin/fasilitas') }}/' + formData.id" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-4">
                @csrf
                <template x-if="modalMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="from_ruang" value="1">

                <!-- Nama Ruang -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Nama Ruang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_fasilitas" x-model="formData.nama_fasilitas" required
                           placeholder="Contoh: Aula FST, Ruang Rapat 1"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                </div>

                <!-- Jenis Ruangan & Kapasitas (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-gray-700">
                                Jenis / Kategori Ruang <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" @click="formData.kategori_id = 'new'" x-show="formData.kategori_id !== 'new'"
                                    class="text-[11px] font-bold text-[#801414] hover:underline cursor-pointer">
                                + Kategori Baru
                            </button>
                        </div>
                        <select name="kategori_id" x-model="formData.kategori_id" required
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all bg-white">
                            <option value="">Pilih Jenis Ruang</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                            <option value="new" class="font-bold text-[#801414]">+ Tambah Kategori Baru...</option>
                        </select>

                        <!-- Input Kategori Baru (Muncul jika pilih Tambah Kategori Baru) -->
                        <div x-show="formData.kategori_id === 'new'" x-transition class="mt-2 p-2.5 bg-marun-50/70 border border-marun-200 rounded-xl space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-bold text-marun-950">Nama Kategori Ruangan Baru:</label>
                                <button type="button" @click="formData.kategori_id = '{{ $kategoriList->first()->id ?? '' }}'" class="text-[10px] text-gray-500 hover:text-gray-700">Batal</button>
                            </div>
                            <input type="text" name="nama_kategori_baru" x-model="formData.nama_kategori_baru" 
                                   :required="formData.kategori_id === 'new'"
                                   placeholder="Contoh: Ruang Sidang, Auditorium..."
                                   class="w-full px-3 py-1.5 bg-white border border-marun-300 rounded-lg text-xs font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#801414]/20 focus:border-[#801414]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Kapasitas Ruang
                        </label>
                        <input type="text" name="kapasitas" x-model="formData.kapasitas"
                               placeholder="Contoh: 100 Orang"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>
                </div>

                <!-- Status & Kode Ruang (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" x-model="formData.status" required
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all bg-white">
                            <option value="tersedia">Tersedia</option>
                            <option value="dipinjam">Digunakan / Dipinjam</option>
                            <option value="perbaikan">Perbaikan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Kode Ruang (Opsional)
                        </label>
                        <input type="text" name="kode_fasilitas" x-model="formData.kode_fasilitas"
                               placeholder="Contoh: AULA-01, RR-01"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>
                </div>

                <!-- Deskripsi / Fasilitas Tambahan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Deskripsi / Fasilitas Ruangan
                    </label>
                    <textarea name="deskripsi" x-model="formData.deskripsi" rows="2"
                              placeholder="Contoh: AC sentral, sound system, proyektor, dan 100 kursi."
                              class="w-full p-3 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all"></textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Fasilitas ini akan tampil di katalog dan halaman detail ruangan pengguna.</p>
                </div>

                <!-- Input Foto Galeri Ruangan (Hingga 3 Foto) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-700">
                            Foto Galeri Ruangan (Maksimal 3 Foto)
                        </label>
                        <span class="text-[10px] text-gray-400">Jika hanya isi 1 foto, sistem otomatis menampilkan foto yang sama</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Foto 1 (Utama) -->
                        <div class="p-2.5 rounded-xl border border-gray-200 bg-gray-50/60 flex flex-col justify-between">
                            <span class="text-[11px] font-bold text-gray-700 mb-1.5 block">Foto 1 (Utama)</span>
                            <div class="aspect-video w-full rounded-lg border border-gray-200 bg-white flex items-center justify-center overflow-hidden mb-2">
                                <template x-if="imagePreview1">
                                    <img :src="imagePreview1" alt="Foto 1" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview1">
                                    <i data-lucide="image" class="w-5 h-5 text-gray-300"></i>
                                </template>
                            </div>
                            <input type="file" name="gambar" accept="image/*" @change="handleImageChange($event, 1)"
                                   class="w-full text-[11px] text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-[#801414]/10 file:text-[#801414] hover:file:bg-[#801414]/20 cursor-pointer">
                        </div>

                        <!-- Foto 2 -->
                        <div class="p-2.5 rounded-xl border border-gray-200 bg-gray-50/60 flex flex-col justify-between">
                            <span class="text-[11px] font-bold text-gray-700 mb-1.5 block">Foto 2 (Opsional)</span>
                            <div class="aspect-video w-full rounded-lg border border-gray-200 bg-white flex items-center justify-center overflow-hidden mb-2">
                                <template x-if="imagePreview2">
                                    <img :src="imagePreview2" alt="Foto 2" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview2">
                                    <i data-lucide="image" class="w-5 h-5 text-gray-300"></i>
                                </template>
                            </div>
                            <input type="file" name="gambar_2" accept="image/*" @change="handleImageChange($event, 2)"
                                   class="w-full text-[11px] text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-[#801414]/10 file:text-[#801414] hover:file:bg-[#801414]/20 cursor-pointer">
                        </div>

                        <!-- Foto 3 -->
                        <div class="p-2.5 rounded-xl border border-gray-200 bg-gray-50/60 flex flex-col justify-between">
                            <span class="text-[11px] font-bold text-gray-700 mb-1.5 block">Foto 3 (Opsional)</span>
                            <div class="aspect-video w-full rounded-lg border border-gray-200 bg-white flex items-center justify-center overflow-hidden mb-2">
                                <template x-if="imagePreview3">
                                    <img :src="imagePreview3" alt="Foto 3" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview3">
                                    <i data-lucide="image" class="w-5 h-5 text-gray-300"></i>
                                </template>
                            </div>
                            <input type="file" name="gambar_3" accept="image/*" @change="handleImageChange($event, 3)"
                                   class="w-full text-[11px] text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-[#801414]/10 file:text-[#801414] hover:file:bg-[#801414]/20 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Modal -->
                <div class="flex gap-2.5 justify-end pt-3 border-t border-gray-100">
                    <button type="button" @click="isModalOpen = false" 
                            class="px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#801414] hover:bg-[#681010] active:scale-[0.99] transition-all shadow-xs cursor-pointer">
                        <span x-text="modalMode === 'add' ? 'Simpan Ruang' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function kelolaRuangHandler() {
        return {
            isModalOpen: false,
            modalMode: 'add',
            imagePreview1: null,
            imagePreview2: null,
            imagePreview3: null,
            formData: {
                id: null,
                nama_fasilitas: '',
                kategori_id: '',
                nama_kategori_baru: '',
                kapasitas: '',
                status: 'tersedia',
                kode_fasilitas: '',
                deskripsi: ''
            },
            handleImageChange(event, index) {
                const file = event.target.files[0];
                if (file) {
                    const url = URL.createObjectURL(file);
                    if (index === 1) this.imagePreview1 = url;
                    if (index === 2) this.imagePreview2 = url;
                    if (index === 3) this.imagePreview3 = url;
                } else {
                    if (index === 1) this.imagePreview1 = null;
                    if (index === 2) this.imagePreview2 = null;
                    if (index === 3) this.imagePreview3 = null;
                }
            },
            openAddModal() {
                this.modalMode = 'add';
                this.imagePreview1 = null;
                this.imagePreview2 = null;
                this.imagePreview3 = null;
                this.formData = {
                    id: null,
                    nama_fasilitas: '',
                    kategori_id: '{{ $kategoriList->first()->id ?? '' }}',
                    nama_kategori_baru: '',
                    kapasitas: '',
                    status: 'tersedia',
                    kode_fasilitas: '',
                    deskripsi: ''
                };
                this.isModalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },
            openEditModal(room) {
                this.modalMode = 'edit';
                this.imagePreview1 = room.gambar_url || null;
                this.imagePreview2 = room.galeri_urls && room.galeri_urls[1] ? room.galeri_urls[1] : null;
                this.imagePreview3 = room.galeri_urls && room.galeri_urls[2] ? room.galeri_urls[2] : null;
                this.formData = {
                    id: room.id,
                    nama_fasilitas: room.nama_fasilitas,
                    kategori_id: room.kategori_id,
                    nama_kategori_baru: '',
                    kapasitas: room.kapasitas || '',
                    status: room.status,
                    kode_fasilitas: room.kode_fasilitas,
                    deskripsi: room.deskripsi || ''
                };
                this.isModalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            }
        };
    }
</script>
@endpush
@endsection
