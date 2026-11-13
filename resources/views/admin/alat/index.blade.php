@extends('layouts.app')

@section('title', 'Kelola Alat - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12" x-data="kelolaAlatHandler()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Kelola Alat
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Kelola data alat dan ketersediaan fasilitas FST
        </p>
    </div>

    <!-- Filter Bar & Tombol Tambah Alat (Sesuai Mockup) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3.5">
        
        <!-- Form Filter (Pencarian & Kategori) -->
        <form method="GET" action="{{ route('admin.alat.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            
            <!-- Pencarian Nama Alat -->
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input type="text" name="cari" value="{{ request('cari') }}" 
                       placeholder="Cari nama alat" 
                       class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
            </div>

            <!-- Dropdown Filter Kategori Alat -->
            <div class="relative sm:w-56 shrink-0">
                <select name="kategori_id" onchange="this.form.submit()"
                        class="w-full appearance-none px-4 py-2.5 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer pr-10">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </div>

            @if(request()->hasAny(['cari', 'kategori_id']))
                <a href="{{ route('admin.alat.index') }}" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs sm:text-sm font-semibold transition-colors flex items-center justify-center gap-1.5 shrink-0" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>

        <!-- Tombol Tambah Alat (Sesuai Mockup: Solid Maroon + Icon) -->
        <button type="button" @click="openAddModal()"
                class="px-5 py-2.5 rounded-xl sm:rounded-2xl bg-[#801414] hover:bg-[#681010] active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer select-none">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Alat</span>
        </button>
    </div>

    <!-- Card Tabel Daftar Alat (Sesuai Mockup) -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-7">
        
        <!-- Header Tabel -->
        <div class="flex items-center gap-2 mb-5">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                Daftar Alat
            </h2>
            <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-0.5 rounded-full font-bold">
                {{ $alat->total() }} Alat
            </span>
        </div>

        <!-- Tabel Data Alat (Bordered Grid Table Sesuai Mockup) -->
        <div class="overflow-x-auto rounded-2xl border border-gray-200">
            <table class="w-full text-xs sm:text-sm text-left border-collapse">
                <thead class="bg-white border-b border-gray-200 text-gray-900 font-bold">
                    <tr>
                        <th class="py-3.5 px-4 text-center border-r border-gray-200 w-14">No</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Nama Alat</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Kategori</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Jumlah</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Kondisi</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Status</th>
                        <th class="py-3.5 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-800">
                    @forelse($alat as $index => $a)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <!-- No -->
                            <td class="py-3.5 px-4 text-center font-semibold text-gray-600 border-r border-gray-200">
                                {{ $loop->iteration + ($alat->currentPage() - 1) * $alat->perPage() }}
                            </td>
                            
                            <!-- Nama Alat (Thumbnail + Nama Sesuai Mockup) -->
                            <td class="py-3.5 px-5 font-bold text-gray-900 border-r border-gray-200 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200/80 flex items-center justify-center overflow-hidden shrink-0 p-1">
                                        @if($a->gambar)
                                            <img src="{{ Storage::url($a->gambar) }}" alt="{{ $a->nama_fasilitas }}" class="w-full h-full object-contain">
                                        @else
                                            @php
                                                $namaLower = strtolower($a->nama_fasilitas);
                                            @endphp
                                            @if(str_contains($namaLower, 'laptop'))
                                                <i data-lucide="laptop" class="w-5 h-5 text-gray-600"></i>
                                            @elseif(str_contains($namaLower, 'mic') || str_contains($namaLower, 'mikrofon'))
                                                <i data-lucide="mic" class="w-5 h-5 text-gray-600"></i>
                                            @elseif(str_contains($namaLower, 'sound') || str_contains($namaLower, 'speaker'))
                                                <i data-lucide="speaker" class="w-5 h-5 text-gray-600"></i>
                                            @elseif(str_contains($namaLower, 'kamera') || str_contains($namaLower, 'camera'))
                                                <i data-lucide="camera" class="w-5 h-5 text-gray-600"></i>
                                            @elseif(str_contains($namaLower, 'hdmi') || str_contains($namaLower, 'kabel') || str_contains($namaLower, 'konverter'))
                                                <i data-lucide="cable" class="w-5 h-5 text-gray-600"></i>
                                            @elseif(str_contains($namaLower, 'proyektor') || str_contains($namaLower, 'infocus'))
                                                <i data-lucide="projector" class="w-5 h-5 text-gray-600"></i>
                                            @else
                                                <i data-lucide="wrench" class="w-5 h-5 text-gray-500"></i>
                                            @endif
                                        @endif
                                    </div>
                                    <span>{{ $a->nama_fasilitas }}</span>
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-5 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">
                                {{ $a->kategori->nama_kategori ?? '-' }}
                            </td>

                            <!-- Jumlah -->
                            <td class="py-3.5 px-5 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">
                                {{ $a->jumlah_formatted }}
                            </td>

                            <!-- Kondisi -->
                            <td class="py-3.5 px-5 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">
                                {{ ucfirst($a->kondisi_awal ?? 'Baik') }}
                            </td>

                            <!-- Status (Badge Sesuai Mockup) -->
                            <td class="py-3.5 px-5 border-r border-gray-200 whitespace-nowrap">
                                @if($a->status === 'tersedia')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tersedia
                                    </span>
                                @elseif($a->status === 'dipinjam')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Rusak
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi (Tombol Edit dan Hapus Sesuai Mockup) -->
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <!-- Tombol Edit (Outlined Red) -->
                                    <button type="button" @click="openEditModal({{ json_encode($a) }})"
                                            class="px-3 py-1.5 rounded-lg border border-rose-300 text-[#801414] hover:bg-rose-50 active:scale-95 text-xs font-bold transition-all inline-flex items-center gap-1 cursor-pointer">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Tombol Hapus (Outlined Gray) -->
                                    <form method="POST" action="{{ route('admin.fasilitas.destroy', $a->id) }}" 
                                          class="inline-block"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus alat {{ addslashes($a->nama_fasilitas) }}?');">
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
                            <td colspan="7" class="py-10 text-center text-gray-500">
                                <i data-lucide="package-open" class="w-10 h-10 mx-auto mb-2 text-gray-400"></i>
                                <p class="font-bold text-gray-800 text-sm">Tidak Ada Data Alat Ditemukan</p>
                                <p class="text-xs text-gray-400 mt-0.5">Silakan tambahkan peralatan baru atau sesuaikan filter pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($alat->hasPages())
            <div class="mt-5 pt-3 border-t border-gray-100">
                {{ $alat->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah / Edit Alat (Interactive Modal) -->
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
                        <i data-lucide="wrench" class="w-5 h-5"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base sm:text-lg" x-text="modalMode === 'add' ? 'Tambah Alat Baru' : 'Edit Data Alat'"></h3>
                </div>
                <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Alat -->
            <form :action="modalMode === 'add' ? '{{ route('admin.fasilitas.store') }}' : '{{ url('admin/fasilitas') }}/' + formData.id" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-4">
                @csrf
                <template x-if="modalMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="from_alat" value="1">

                <!-- Nama Alat -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Nama Alat <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_fasilitas" x-model="formData.nama_fasilitas" required
                           placeholder="Contoh: Infocus, Laptop, Mikrofon, Sound System"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                </div>

                <!-- Kategori & Jumlah (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select name="kategori_id" x-model="formData.kategori_id" required
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all bg-white">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Jumlah Unit
                        </label>
                        <input type="text" name="jumlah" x-model="formData.jumlah"
                               placeholder="Contoh: 5 Unit, 10 Unit"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>
                </div>

                <!-- Kondisi & Status (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Kondisi Alat <span class="text-rose-500">*</span>
                        </label>
                        <select name="kondisi_awal" x-model="formData.kondisi_awal" required
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all bg-white">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Status Ketersediaan <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" x-model="formData.status" required
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all bg-white">
                            <option value="tersedia">Tersedia</option>
                            <option value="dipinjam">Digunakan / Dipinjam</option>
                            <option value="perbaikan">Rusak / Perbaikan</option>
                        </select>
                    </div>
                </div>

                <!-- Kode Alat & Foto Gambar (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Kode Alat (Opsional)
                        </label>
                        <input type="text" name="kode_fasilitas" x-model="formData.kode_fasilitas"
                               placeholder="Contoh: INF-01, LPT-01"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Upload Foto Alat (Opsional)
                        </label>
                        <input type="file" name="gambar" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs text-gray-600 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all bg-white file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    </div>
                </div>

                <!-- Deskripsi / Catatan Tambahan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Deskripsi / Spesifikasi Tambahan
                    </label>
                    <textarea name="deskripsi" x-model="formData.deskripsi" rows="2"
                              placeholder="Contoh: Kabel power dan remote lengkap, tas pelindung tersedia."
                              class="w-full p-3 border border-gray-300 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all"></textarea>
                </div>

                <!-- Tombol Aksi Modal -->
                <div class="flex gap-2.5 justify-end pt-3 border-t border-gray-100">
                    <button type="button" @click="isModalOpen = false" 
                            class="px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#801414] hover:bg-[#681010] active:scale-[0.99] transition-all shadow-xs cursor-pointer">
                        <span x-text="modalMode === 'add' ? 'Simpan Alat' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function kelolaAlatHandler() {
        return {
            isModalOpen: false,
            modalMode: 'add',
            formData: {
                id: null,
                nama_fasilitas: '',
                kategori_id: '',
                jumlah: '',
                kondisi_awal: 'Baik',
                status: 'tersedia',
                kode_fasilitas: '',
                deskripsi: ''
            },
            openAddModal() {
                this.modalMode = 'add';
                this.formData = {
                    id: null,
                    nama_fasilitas: '',
                    kategori_id: '{{ $kategoriList->first()->id ?? '' }}',
                    jumlah: '1 Unit',
                    kondisi_awal: 'Baik',
                    status: 'tersedia',
                    kode_fasilitas: '',
                    deskripsi: ''
                };
                this.isModalOpen = true;
            },
            openEditModal(tool) {
                this.modalMode = 'edit';
                this.formData = {
                    id: tool.id,
                    nama_fasilitas: tool.nama_fasilitas,
                    kategori_id: tool.kategori_id,
                    jumlah: tool.jumlah || '1 Unit',
                    kondisi_awal: tool.kondisi_awal || 'Baik',
                    status: tool.status,
                    kode_fasilitas: tool.kode_fasilitas,
                    deskripsi: tool.deskripsi || ''
                };
                this.isModalOpen = true;
            }
        };
    }
</script>
@endpush
@endsection
