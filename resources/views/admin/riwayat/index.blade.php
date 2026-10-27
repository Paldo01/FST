@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" x-data="riwayatHandler()">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            Riwayat Peminjaman
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Kelola dan pantau seluruh riwayat peminjaman ruang dan alat FST
        </p>
    </div>

    <!-- Filter Bar (Sesuai Mockup) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.riwayat.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            
            <!-- Pencarian Nama Peminjam atau Fasilitas -->
            <div class="relative flex-1 min-w-[240px]">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input type="text" name="cari" value="{{ request('cari') }}" 
                       placeholder="Cari nama peminjam atau fasilitas" 
                       class="w-full pl-11 pr-4 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
            </div>

            <!-- Dropdown Status -->
            <div class="relative md:w-52 shrink-0">
                <select name="status" 
                        class="w-full appearance-none px-4 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer pr-10">
                    <option value="">Semua Status</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                </select>
                <i data-lucide="chevron-down" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </div>

            <!-- Input Tanggal (Menampilkan 'Pilih Tanggal' Sesuai Mockup) -->
            <div class="relative md:w-48 shrink-0">
                <input type="{{ request('tanggal') ? 'date' : 'text' }}" 
                       name="tanggal" 
                       value="{{ request('tanggal') }}" 
                       placeholder="Pilih Tanggal"
                       onfocus="(this.type='date')"
                       onblur="if(!this.value){this.type='text'}"
                       class="w-full pl-4 pr-10 py-2.5 sm:py-3 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs cursor-pointer">
                <i data-lucide="calendar" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </div>

            <!-- Tombol Cari (Solid Maroon Sesuai Mockup) -->
            <button type="submit" 
                    class="px-6 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl bg-[#801414] hover:bg-[#681010] active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer select-none">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Cari</span>
            </button>

            <!-- Reset Filter -->
            @if(request()->hasAny(['cari', 'status', 'tanggal']))
                <a href="{{ route('admin.riwayat.index') }}" 
                   class="px-3.5 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold transition-colors flex items-center justify-center gap-1.5 shrink-0" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span class="md:hidden lg:inline">Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Container Tabel Daftar Riwayat Peminjaman (Sesuai Mockup) -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-7 space-y-5">
        
        <!-- Header Tabel & Badge Counter -->
        <div class="flex items-center gap-2.5">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                Daftar Riwayat Peminjaman
            </h2>
            <span class="bg-gray-100 text-gray-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-gray-200/60">
                {{ $riwayat->total() }} Riwayat
            </span>
        </div>

        <!-- Tabel Grid Bordered (Sesuai Mockup) -->
        <div class="overflow-x-auto rounded-2xl border border-gray-200">
            <table class="w-full text-xs sm:text-sm text-left border-collapse">
                <thead class="bg-white border-b border-gray-200 text-gray-900 font-bold">
                    <tr>
                        <th class="py-3.5 px-4 text-center border-r border-gray-200 w-12 sm:w-14">No</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Peminjam</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Fasilitas</th>
                        <th class="py-3.5 px-5 border-r border-gray-200 whitespace-nowrap">Tanggal</th>
                        <th class="py-3.5 px-5 border-r border-gray-200 whitespace-nowrap">Waktu</th>
                        <th class="py-3.5 px-5 border-r border-gray-200">Keperluan</th>
                        <th class="py-3.5 px-5 border-r border-gray-200 text-center whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-800">
                    @php
                        // Palet warna avatar pastel bergantian sesuai mockup
                        $avatarColors = [
                            ['bg' => 'bg-red-50', 'text' => 'text-red-500', 'border' => 'border-red-100'],
                            ['bg' => 'bg-blue-50', 'text' => 'text-blue-500', 'border' => 'border-blue-100'],
                            ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'border' => 'border-emerald-100'],
                            ['bg' => 'bg-rose-50', 'text' => 'text-rose-500', 'border' => 'border-rose-100'],
                            ['bg' => 'bg-amber-50', 'text' => 'text-amber-500', 'border' => 'border-amber-100'],
                            ['bg' => 'bg-purple-50', 'text' => 'text-purple-500', 'border' => 'border-purple-100'],
                            ['bg' => 'bg-teal-50', 'text' => 'text-teal-500', 'border' => 'border-teal-100'],
                        ];
                    @endphp

                    @forelse($riwayat as $index => $p)
                        @php
                            $c = $avatarColors[$loop->index % count($avatarColors)];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            
                            <!-- No -->
                            <td class="py-3.5 px-4 text-center font-semibold text-gray-600 border-r border-gray-200">
                                {{ $loop->iteration + ($riwayat->currentPage() - 1) * $riwayat->perPage() }}
                            </td>

                            <!-- Peminjam (Avatar Warna Warni + Nama & Peran Sesuai Mockup) -->
                            <td class="py-3.5 px-5 border-r border-gray-200 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }} flex items-center justify-center shrink-0 border shadow-2xs">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-bold text-gray-900">
                                            {{ $p->pengguna->nama_lengkap }}
                                        </span>
                                        <span class="text-gray-600 font-semibold text-xs ml-0.5">
                                            ({{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }})
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Fasilitas -->
                            <td class="py-3.5 px-5 font-bold text-gray-900 border-r border-gray-200 whitespace-nowrap">
                                {{ $p->fasilitas->nama_fasilitas }}
                            </td>

                            <!-- Tanggal (Format: 20 sept 2026 Sesuai Mockup) -->
                            <td class="py-3.5 px-5 text-gray-700 font-medium border-r border-gray-200 whitespace-nowrap">
                                {{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d M Y') : '-' }}
                            </td>

                            <!-- Waktu (Format: 08.00 - 12.00 Sesuai Mockup) -->
                            <td class="py-3.5 px-5 text-gray-700 font-medium border-r border-gray-200 whitespace-nowrap">
                                {{ $p->tanggal_mulai ? $p->tanggal_mulai->format('H.i') : '' }} - {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('H.i') : '' }}
                            </td>

                            <!-- Keperluan -->
                            <td class="py-3.5 px-5 text-gray-800 font-medium border-r border-gray-200 max-w-xs">
                                {{ $p->tujuan_peminjaman }}
                            </td>

                            <!-- Status (Badge Warna Sesuai Mockup) -->
                            <td class="py-3.5 px-5 text-center border-r border-gray-200 whitespace-nowrap">
                                @if($p->status_pengajuan === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#DCFCE7] text-[#15803D] border border-[#86EFAC]/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A]"></span>
                                        <span>Disetujui</span>
                                    </span>
                                @elseif($p->status_pengajuan === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#DBEAFE] text-[#1D4ED8] border border-[#93C5FD]/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
                                        <span>Selesai</span>
                                    </span>
                                @elseif($p->status_pengajuan === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FEE2E2] text-[#B91C1C] border border-[#FCA5A5]/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626]"></span>
                                        <span>Ditolak</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FEF9C3] text-[#A16207] border border-[#FDE047]/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#CA8A04]"></span>
                                        <span>Menunggu</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi: Tombol Detail (Sesuai Mockup: Border Putih + Icon Eye) -->
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="bukaModalDetail({{ $p->id }}, '{{ addslashes($p->pengguna->nama_lengkap) }}', '{{ ucfirst($p->pengguna->jenis_pengguna ?? 'Mahasiswa') }}', '{{ $p->pengguna->nomor_identitas ?? '-' }}', '{{ $p->pengguna->nomor_telepon ?? '-' }}', '{{ $p->pengguna->email }}', '{{ addslashes($p->fasilitas->nama_fasilitas) }}', '{{ $p->fasilitas->kode_fasilitas }}', '{{ $p->fasilitas->kategori->nama_kategori ?? '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->translatedFormat('d F Y') : '-' }}', '{{ $p->tanggal_mulai ? $p->tanggal_mulai->format('H.i') : '' }} - {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('H.i') : '' }}', '{{ addslashes($p->tujuan_peminjaman) }}', '{{ $p->status_pengajuan }}', '{{ addslashes($p->alasan_penolakan ?? '') }}', '{{ $p->berkas_persyaratan ? Storage::url($p->berkas_persyaratan) : '' }}', '{{ $p->created_at ? $p->created_at->translatedFormat('d F Y, H.i') : '-' }}', '{{ $p->pengembalian ? $p->pengembalian->tanggal_pengembalian->translatedFormat('d F Y, H.i') : '-' }}', '{{ $p->pengembalian ? ucfirst($p->pengembalian->kondisi_akhir) : '-' }}', '{{ addslashes($p->pengembalian->catatan_admin ?? '-') }}')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition-all shadow-2xs active:scale-95 cursor-pointer">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-gray-500"></i>
                                    <span>Detail</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-2"></i>
                                <p class="text-sm font-semibold text-gray-700">Tidak ada riwayat peminjaman ditemukan</p>
                                <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci pencarian atau filter yang Anda gunakan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($riwayat->hasPages())
            <div class="pt-4 border-t border-gray-100">
                {{ $riwayat->links() }}
            </div>
        @endif

    </div>

    <!-- ========================================== -->
    <!-- MODAL: Detail Riwayat Peminjaman           -->
    <!-- ========================================== -->
    <div x-show="modalDetailOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="modalDetailOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-gray-100 z-10 space-y-5">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#801414] border border-red-100 flex items-center justify-center shrink-0">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">
                                Detail Riwayat Peminjaman
                            </h3>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                Diajukan pada <span x-text="detailData.diajukanPada"></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="modalDetailOpen = false" class="text-gray-400 hover:text-gray-700 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Konten Detail -->
                <div class="space-y-4 text-xs sm:text-sm">
                    
                    <!-- Box 1: Info Pemohon -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Pemohon</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-gray-500 block text-[11px]">Nama Pemohon</span>
                                <span class="font-bold text-gray-900" x-text="detailData.namaPemohon"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Peran / Identitas</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.jenisPemohon + ' (' + detailData.nomorIdentitas + ')'"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Email</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.email"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">No. Telepon / WA</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.nomorTelepon"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Info Fasilitas & Jadwal -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Fasilitas & Jadwal</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-gray-500 block text-[11px]">Nama Fasilitas</span>
                                <span class="font-bold text-gray-900" x-text="detailData.namaFasilitas"></span>
                                <span class="text-[11px] text-gray-500 block" x-text="'Kode: ' + detailData.kodeFasilitas"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Kategori</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.kategori"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Tanggal Pelaksanaan</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.tanggal"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px]">Rentang Waktu</span>
                                <span class="font-semibold text-gray-800" x-text="detailData.waktu"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Box 3: Keperluan -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Keperluan / Agenda</h4>
                        <p class="text-gray-900 font-medium leading-relaxed" x-text="detailData.keperluan"></p>
                    </div>

                    <!-- Box 4: Berkas Persyaratan Jika Ada -->
                    <template x-if="detailData.berkasUrl">
                        <div class="bg-blue-50/60 rounded-2xl p-4 border border-blue-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                                    <i data-lucide="file-check" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 text-xs">Berkas Lampiran Persyaratan</h5>
                                    <p class="text-[11px] text-gray-500">Tersedia dokumen peminjaman</p>
                                </div>
                            </div>
                            <a :href="detailData.berkasUrl" target="_blank" 
                               class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors shadow-2xs">
                                <span>Lihat Berkas</span>
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </template>

                    <!-- Box 5: Status Peminjaman -->
                    <div class="p-3.5 rounded-2xl border flex items-center justify-between"
                         :class="{
                             'bg-emerald-50/80 border-emerald-200 text-emerald-800': detailData.status === 'disetujui',
                             'bg-blue-50/80 border-blue-200 text-blue-800': detailData.status === 'selesai',
                             'bg-red-50/80 border-red-200 text-red-800': detailData.status === 'ditolak',
                             'bg-amber-50/80 border-amber-200 text-amber-800': detailData.status === 'menunggu'
                         }">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider block">Status Transaksi</span>
                            <span class="font-black text-sm capitalize" x-text="detailData.status"></span>
                        </div>
                        <template x-if="detailData.status === 'ditolak' && detailData.alasanPenolakan">
                            <div class="text-right max-w-xs">
                                <span class="text-[11px] font-bold text-red-600 block">Alasan Ditolak:</span>
                                <span class="text-xs text-red-800 italic" x-text="detailData.alasanPenolakan"></span>
                            </div>
                        </template>
                        <template x-if="detailData.status === 'selesai'">
                            <div class="text-right">
                                <span class="text-[11px] font-bold text-blue-600 block">Dikembalikan:</span>
                                <span class="text-xs text-blue-900 font-semibold" x-text="detailData.tglPengembalian"></span>
                                <span class="text-[10px] text-blue-700 block" x-text="'Kondisi: ' + detailData.kondisiAkhir"></span>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Footer Modal -->
                <div class="pt-2 flex justify-end">
                    <button type="button" @click="modalDetailOpen = false" 
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs sm:text-sm transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Alpine.js Handler -->
<script>
    function riwayatHandler() {
        return {
            modalDetailOpen: false,
            detailData: {
                id: null,
                namaPemohon: '',
                jenisPemohon: '',
                nomorIdentitas: '',
                nomorTelepon: '',
                email: '',
                namaFasilitas: '',
                kodeFasilitas: '',
                kategori: '',
                tanggal: '',
                waktu: '',
                keperluan: '',
                status: '',
                alasanPenolakan: '',
                berkasUrl: '',
                diajukanPada: '',
                tglPengembalian: '',
                kondisiAkhir: '',
                catatanPengembalian: ''
            },

            bukaModalDetail(id, namaPemohon, jenisPemohon, nomorIdentitas, nomorTelepon, email, namaFasilitas, kodeFasilitas, kategori, tanggal, waktu, keperluan, status, alasanPenolakan, berkasUrl, diajukanPada, tglPengembalian, kondisiAkhir, catatanPengembalian) {
                this.detailData = {
                    id: id,
                    namaPemohon: namaPemohon,
                    jenisPemohon: jenisPemohon,
                    nomorIdentitas: nomorIdentitas,
                    nomorTelepon: nomorTelepon,
                    email: email,
                    namaFasilitas: namaFasilitas,
                    kodeFasilitas: kodeFasilitas,
                    kategori: kategori,
                    tanggal: tanggal,
                    waktu: waktu,
                    keperluan: keperluan,
                    status: status,
                    alasanPenolakan: alasanPenolakan,
                    berkasUrl: berkasUrl,
                    diajukanPada: diajukanPada,
                    tglPengembalian: tglPengembalian,
                    kondisiAkhir: kondisiAkhir,
                    catatanPengembalian: catatanPengembalian
                };
                this.modalDetailOpen = true;
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            }
        };
    }
</script>
@endsection
