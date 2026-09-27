@extends('layouts.app')

@section('title', 'Buat Peminjaman')
@section('header', 'Ajukan Peminjaman')
@section('description', 'Pilih fasilitas dan jadwal yang tersedia untuk dipinjam.')

@section('content')
<div class="max-w-3xl">
    <x-card>
        <form method="POST" action="{{ route('peminjaman.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="peminjamanForm()">
            @csrf

            <!-- Fasilitas Selection -->
            <div>
                <label for="fasilitas_id" class="block text-sm font-medium text-gray-700 mb-1">Ruangan <span class="text-red-500">*</span></label>
                <select name="fasilitas_id" id="fasilitas_id" required x-model="fasilitasId" @change="cekKetersediaan"
                    class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3 border shadow-sm">
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($fasilitas as $f)
                        <option value="{{ $f->id }}" {{ old('fasilitas_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->nama_fasilitas }} ({{ ucfirst($f->kategori->jenis) }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Catatan: Peminjaman peralatan/barang hanya dapat dilakukan langsung di loket (lewat Admin).</p>
                @error('fasilitas_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" required x-model="start" @change="cekKetersediaan"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        value="{{ old('tanggal_mulai') }}"
                        class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3 border shadow-sm">
                    @error('tanggal_mulai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" required x-model="end" @change="cekKetersediaan"
                        value="{{ old('tanggal_selesai') }}"
                        class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3 border shadow-sm">
                    @error('tanggal_selesai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Validation Feedback (Live Checking) -->
            <div x-show="checking" class="text-sm text-gray-500 flex items-center gap-2">
                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Mengecek jadwal...
            </div>
            
            <div x-show="!checking && statusMsg !== ''" class="p-3 rounded-lg border text-sm"
                :class="isAvailable ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
                style="display: none;">
                <div class="flex items-start gap-2">
                    <i :data-lucide="isAvailable ? 'check-circle' : 'alert-circle'" class="w-5 h-5 mt-0.5 shrink-0"></i>
                    <p x-text="statusMsg"></p>
                </div>
            </div>

            <!-- Deskripsi / Tujuan -->
            <div>
                <label for="tujuan_peminjaman" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Kegiatan <span class="text-red-500">*</span></label>
                <textarea name="tujuan_peminjaman" id="tujuan_peminjaman" rows="3" required
                    class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3 border shadow-sm"
                    placeholder="Jelaskan tujuan peminjaman secara detail...">{{ old('tujuan_peminjaman') }}</textarea>
                @error('tujuan_peminjaman') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Upload File -->
            <div>
                <label for="berkas_persyaratan" class="block text-sm font-medium text-gray-700 mb-1">Berkas Persyaratan (Surat Peminjaman)</label>
                <p class="text-xs text-gray-500 mb-2">Opsional. Format PDF maksimal 5MB.</p>
                <input type="file" name="berkas_persyaratan" id="berkas_persyaratan" accept=".pdf"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-marun-50 file:text-marun-700 hover:file:bg-marun-100">
                @error('berkas_persyaratan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">Batal</a>
                <button type="submit" :disabled="!isAvailable && start && end && fasilitasId" 
                    class="px-4 py-2 bg-marun-800 text-white font-medium rounded-lg hover:bg-marun-700 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('peminjamanForm', () => ({
            fasilitasId: '{{ old('fasilitas_id', '') }}',
            start: '{{ old('tanggal_mulai', '') }}',
            end: '{{ old('tanggal_selesai', '') }}',
            checking: false,
            isAvailable: true,
            statusMsg: '',
            
            cekKetersediaan() {
                if (!this.fasilitasId || !this.start || !this.end) {
                    this.statusMsg = '';
                    return;
                }
                
                this.checking = true;
                this.statusMsg = '';
                
                fetch('{{ route('api.cek_ketersediaan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        fasilitas_id: this.fasilitasId,
                        tanggal_mulai: this.start,
                        tanggal_selesai: this.end
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.checking = false;
                    this.isAvailable = data.tersedia;
                    this.statusMsg = data.pesan;
                    setTimeout(() => lucide.createIcons(), 10);
                })
                .catch(() => {
                    this.checking = false;
                    this.statusMsg = 'Gagal mengecek jadwal.';
                });
            }
        }));
    });
</script>
@endpush
@endsection
