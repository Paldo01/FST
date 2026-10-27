@extends('layouts.app')

@section('title', 'Proses Pengembalian')
@section('header', 'Proses Pengembalian')
@section('description', 'Catat pengembalian alat dan ruangan yang telah selesai digunakan.')

@section('content')
<x-card>
    <div x-data="{ showModal: false, selectedId: null, selectedName: '', selectedUser: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3">Peminjam</th>
                        <th scope="col" class="px-6 py-3">Fasilitas</th>
                        <th scope="col" class="px-6 py-3">Tgl Diambil (Mulai)</th>
                        <th scope="col" class="px-6 py-3">Kondisi Awal</th>
                        <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamanAktif as $p)
                        <tr class="bg-white border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 block">{{ $p->pengguna->nama_lengkap }}</span>
                                <span class="text-xs text-gray-500">{{ $p->pengguna->nomor_identitas }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-marun-900">{{ $p->fasilitas->nama_fasilitas }}</span>
                                <span class="block text-xs text-gray-500 mt-1 capitalize">{{ $p->fasilitas->kategori->jenis }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span>{{ $p->tanggal_mulai->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="uppercase text-xs font-medium">{{ $p->fasilitas->kondisi_awal }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="selectedId = {{ $p->id }}; selectedName = '{{ $p->fasilitas->nama_fasilitas }}'; selectedUser = '{{ $p->pengguna->nama_lengkap }}'; showModal = true" 
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-marun-800 text-white hover:bg-marun-700 text-xs font-medium rounded-lg transition-colors">
                                    <i data-lucide="refresh-ccw" class="w-3.5 h-3.5"></i> Proses
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i data-lucide="check-square" class="w-10 h-10 mx-auto text-gray-300 mb-2"></i>
                                Tidak ada fasilitas yang menunggu pengembalian saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Alpine Modal for Return Processing -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="showModal = false" class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden" x-transition>
                <div class="bg-marun-900 p-4 flex justify-between items-center text-white">
                    <h3 class="font-bold">Form Pengembalian Fasilitas</h3>
                    <button @click="showModal = false" class="text-marun-200 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="p-5 bg-marun-50/30 border-b border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Fasilitas & Peminjam</p>
                    <p class="font-bold text-gray-900 text-lg mt-1" x-text="selectedName"></p>
                    <p class="text-sm text-gray-600 mt-0.5" x-text="selectedUser"></p>
                </div>

                <form :action="'/admin/pengembalian/' + selectedId" method="POST" class="p-5 space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Akhir Saat Dikembalikan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer border border-gray-200 rounded-lg p-3 flex flex-col items-center text-center hover:bg-gray-50 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                <input type="radio" name="kondisi_akhir" value="baik" class="sr-only" required checked>
                                <i data-lucide="thumbs-up" class="w-6 h-6 text-green-600 mb-1"></i>
                                <span class="text-xs font-medium text-gray-900">Baik</span>
                            </label>
                            
                            <label class="cursor-pointer border border-gray-200 rounded-lg p-3 flex flex-col items-center text-center hover:bg-gray-50 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="kondisi_akhir" value="rusak" class="sr-only">
                                <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600 mb-1"></i>
                                <span class="text-xs font-medium text-gray-900">Rusak</span>
                            </label>
                            
                            <label class="cursor-pointer border border-gray-200 rounded-lg p-3 flex flex-col items-center text-center hover:bg-gray-50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="kondisi_akhir" value="hilang" class="sr-only">
                                <i data-lucide="help-circle" class="w-6 h-6 text-red-600 mb-1"></i>
                                <span class="text-xs font-medium text-gray-900">Hilang</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="catatan_admin" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan_admin" id="catatan_admin" rows="3" 
                            class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm"
                            placeholder="Catatan terkait kerusakan, keterlambatan, atau denda..."></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-marun-800 text-white text-sm font-medium rounded-lg hover:bg-marun-700">Simpan Pengembalian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-card>
@endsection
