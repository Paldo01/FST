@extends('layouts.app')

@section('title', 'Verifikasi Peminjaman')
@section('header', 'Verifikasi Peminjaman')
@section('description', 'Persetujuan atau penolakan pengajuan peminjaman baru.')

@section('content')
<x-card>
    <div x-data="{ tab: 'menunggu' }">
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6 flex gap-6 px-2">
            <button @click="tab = 'menunggu'" class="py-3 px-2 border-b-2 font-medium text-sm transition-colors"
                :class="tab === 'menunggu' ? 'border-marun-800 text-marun-800' : 'border-transparent text-gray-500 hover:text-gray-700'">
                Menunggu Persetujuan
                @if($pengajuan->where('status_pengajuan', 'menunggu')->count() > 0)
                    <span class="ml-2 bg-mustard-500 text-marun-950 text-xs px-2 py-0.5 rounded-full">{{ $pengajuan->where('status_pengajuan', 'menunggu')->count() }}</span>
                @endif
            </button>
            <button @click="tab = 'riwayat'" class="py-3 px-2 border-b-2 font-medium text-sm transition-colors"
                :class="tab === 'riwayat' ? 'border-marun-800 text-marun-800' : 'border-transparent text-gray-500 hover:text-gray-700'">
                Riwayat Keputusan
            </button>
        </div>

        <!-- Tab: Menunggu -->
        <div x-show="tab === 'menunggu'" x-transition>
            @php $menunggu = $pengajuan->where('status_pengajuan', 'menunggu'); @endphp
            
            <div class="space-y-4">
                @forelse($menunggu as $p)
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow" x-data="{ showTolak: false }">
                        <div class="flex flex-col lg:flex-row gap-6">
                            <!-- User & Facility Info -->
                            <div class="flex-1 space-y-4">
                                <div class="flex items-start gap-4 border-b border-gray-100 pb-4">
                                    <div class="w-12 h-12 bg-marun-50 rounded-lg flex items-center justify-center text-marun-800 shrink-0">
                                        <i data-lucide="{{ $p->fasilitas->kategori->jenis === 'ruangan' ? 'door-open' : 'monitor' }}" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-lg">{{ $p->fasilitas->nama_fasilitas }}</h4>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">{{ $p->fasilitas->kode_fasilitas }} &bull; {{ $p->fasilitas->kategori->nama_kategori }}</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500 block text-xs mb-1">Pemohon</span>
                                        <span class="font-medium text-gray-900 block">{{ $p->pengguna->nama_lengkap }}</span>
                                        <span class="text-xs text-gray-500">{{ $p->pengguna->nomor_identitas }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block text-xs mb-1">Jadwal</span>
                                        <span class="font-medium text-gray-900 block">{{ $p->tanggal_mulai->format('d/m/Y H:i') }}</span>
                                        <span class="text-xs text-gray-500 block">s.d {{ $p->tanggal_selesai->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="col-span-2 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-gray-500 block text-xs mb-1">Tujuan / Kegiatan</span>
                                        <span class="font-medium text-gray-900">{{ $p->tujuan_peminjaman }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="lg:w-64 shrink-0 flex flex-col justify-center gap-3 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6">
                                @if($p->berkas_persyaratan)
                                    <a href="{{ Storage::url($p->berkas_persyaratan) }}" target="_blank" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 mb-2">
                                        <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i> Cek Berkas
                                    </a>
                                @endif

                                <!-- Form Setuju -->
                                <form method="POST" action="{{ route('admin.verifikasi.setujui', $p->id) }}">
                                    @csrf
                                    <button type="button" @click="if(confirm('Setujui peminjaman ini?')) $el.form.submit()" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-green-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-green-700">
                                        <i data-lucide="check" class="w-4 h-4"></i> Setujui Pengajuan
                                    </button>
                                </form>
                                
                                <button type="button" @click="showTolak = !showTolak" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 rounded-lg text-sm font-medium">
                                    <i data-lucide="x" class="w-4 h-4"></i> Tolak Pengajuan
                                </button>
                                
                                <!-- Form Tolak (Hidden initially) -->
                                <div x-show="showTolak" x-transition class="mt-3 bg-white p-3 border border-red-200 rounded-lg shadow-sm">
                                    <form method="POST" action="{{ route('admin.verifikasi.tolak', $p->id) }}">
                                        @csrf
                                        <textarea name="alasan_penolakan" required rows="2" class="w-full text-sm border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-500 mb-2" placeholder="Alasan penolakan..."></textarea>
                                        <div class="flex gap-2">
                                            <button type="button" @click="showTolak = false" class="flex-1 py-1.5 text-xs text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Batal</button>
                                            <button type="submit" class="flex-1 py-1.5 text-xs text-white bg-red-600 rounded hover:bg-red-700 font-medium">Kirim Penolakan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                        <h4 class="text-lg font-medium text-gray-900">Belum ada pengajuan</h4>
                        <p class="text-gray-500 text-sm mt-1">Saat ini tidak ada permohonan peminjaman yang perlu diverifikasi.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab: Riwayat -->
        <div x-show="tab === 'riwayat'" x-transition style="display: none;">
            @php $riwayat = $pengajuan->whereIn('status_pengajuan', ['disetujui', 'ditolak'])->take(20); @endphp
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3">Pemohon</th>
                            <th scope="col" class="px-6 py-3">Fasilitas</th>
                            <th scope="col" class="px-6 py-3">Jadwal</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-right">Waktu Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $p)
                            <tr class="bg-white border-b border-gray-50">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900 block">{{ $p->pengguna->nama_lengkap }}</span>
                                    <span class="text-xs text-gray-500">{{ $p->pengguna->nomor_identitas }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $p->fasilitas->nama_fasilitas }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $p->tanggal_mulai->format('d/m/Y') }}<br>
                                    <span class="text-xs text-gray-500">s.d {{ $p->tanggal_selesai->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :status="$p->status_pengajuan" />
                                    @if($p->status_pengajuan === 'ditolak')
                                        <p class="text-xs text-red-500 mt-1" title="{{ $p->alasan_penolakan }}">{{ Str::limit($p->alasan_penolakan, 30) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-xs">
                                    {{ $p->updated_at->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada riwayat verifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-card>
@endsection
