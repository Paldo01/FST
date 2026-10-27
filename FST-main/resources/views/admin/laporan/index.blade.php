@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('header', 'Laporan & Rekapitulasi')
@section('description', 'Cetak laporan peminjaman fasilitas dalam rentang waktu tertentu.')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <x-card class="md:col-span-1 border-t-4 border-t-marun-800">
        <x-slot name="header">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="printer" class="w-5 h-5 text-marun-800"></i> Cetak Laporan PDF
            </h3>
        </x-slot>

        <form method="GET" action="{{ route('admin.laporan.cetak') }}" target="_blank" class="space-y-4">
            <div>
                <label for="tanggal_awal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_awal" id="tanggal_awal" required value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                    class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3">
            </div>
            
            <div>
                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" required value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                    class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Peminjaman</label>
                <select name="status" id="status" class="block w-full rounded-lg border-gray-300 focus:ring-mustard-400 focus:border-mustard-400 text-sm py-2 px-3">
                    <option value="">Semua Status</option>
                    <option value="selesai">Selesai / Dikembalikan</option>
                    <option value="disetujui">Disetujui / Sedang Berjalan</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="w-full flex justify-center items-center gap-2 px-4 py-2 bg-marun-800 hover:bg-marun-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
                    <i data-lucide="file-down" class="w-4 h-4"></i> Generate PDF
                </button>
            </div>
        </form>
    </x-card>

    <x-card class="md:col-span-2">
        <x-slot name="header">
            <h3 class="text-lg font-bold text-gray-900">Rekapitulasi Cepat (Bulan Ini)</h3>
        </x-slot>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Peminjaman</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistikBulanIni['total'] }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-xl border border-green-100 text-center">
                <p class="text-xs text-green-600 uppercase tracking-wide mb-1">Selesai</p>
                <p class="text-2xl font-bold text-green-700">{{ $statistikBulanIni['selesai'] }}</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 text-center">
                <p class="text-xs text-blue-600 uppercase tracking-wide mb-1">Berjalan</p>
                <p class="text-2xl font-bold text-blue-700">{{ $statistikBulanIni['berjalan'] }}</p>
            </div>
            <div class="p-4 bg-red-50 rounded-xl border border-red-100 text-center">
                <p class="text-xs text-red-600 uppercase tracking-wide mb-1">Ditolak</p>
                <p class="text-2xl font-bold text-red-700">{{ $statistikBulanIni['ditolak'] }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-2">Fasilitas Paling Sering Dipinjam</th>
                        <th scope="col" class="px-4 py-2 text-center">Frekuensi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fasilitasTerpopuler as $f)
                        <tr class="bg-white border-b border-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $f->nama_fasilitas }}
                                <span class="text-xs text-gray-400 font-normal ml-2">({{ $f->kode_fasilitas }})</span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-marun-800">
                                {{ $f->peminjaman_count }} kali
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-6 text-center text-gray-500">Belum ada data peminjaman bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
