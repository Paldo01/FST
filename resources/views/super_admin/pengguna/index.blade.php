@extends('layouts.app')

@section('title', 'Kelola Admin - Sistem Peminjaman FST')

@section('content')
<div class="space-y-6 pb-12 pt-1 sm:pt-2" 
     x-data="kelolaAdminHandler({{ json_encode($defaultAdmin) }})">

    <!-- Header Halaman (Sesuai Mockup) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                Kelola Admin
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Kelola data admin sistem peminjaman fasilitas FST
            </p>
        </div>

        <!-- Tombol Tambah Admin (Sesuai Mockup) -->
        <button type="button" 
                @click="openAddModal()"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 sm:py-3 bg-[#801414] hover:bg-[#681010] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-2xl shadow-sm hover:shadow-md transition-all shrink-0 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span>Tambah Admin</span>
        </button>
    </div>

    <!-- Tab Pilihan (Admin FST & Pengguna Lainnya) -->
    <div class="flex items-center gap-2 border-b border-gray-100 pb-2">
        <button type="button" 
                @click="activeTab = 'admin'" 
                :class="activeTab === 'admin' ? 'bg-[#801414] text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-2xs">
            Admin FST ({{ $adminList->total() }})
        </button>
        <button type="button" 
                @click="activeTab = 'pengguna_lain'" 
                :class="activeTab === 'pengguna_lain' ? 'bg-[#801414] text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-2xs">
            Mahasiswa & Dosen
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: KELOLA ADMIN FST (SESUAI MOCKUP)   -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'admin'" class="space-y-6">

        <!-- Kartu Sorotan / Detail Admin Terpilih (Sesuai Mockup) -->
        <template x-if="selectedAdmin">
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100/90 shadow-2xs p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 transition-all">
                
                <!-- Sisi Kiri: Foto/Ikon Avatar & Data Detail -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-7">
                    <!-- Avatar Lingkaran Soft Pink -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-[#FEE2E2] flex items-center justify-center shrink-0">
                        <i data-lucide="user" class="w-10 h-10 sm:w-12 sm:h-12 text-[#EF4444]"></i>
                    </div>

                    <!-- Informasi Rinci dengan Titik Dua Sejajar -->
                    <div class="grid grid-cols-[auto_auto_1fr] gap-x-3 gap-y-1.5 text-xs sm:text-sm leading-relaxed">
                        <span class="text-gray-500 font-medium">Nama admin</span>
                        <span class="text-gray-500 font-medium">:</span>
                        <span class="font-bold text-gray-900" x-text="selectedAdmin.nama_lengkap"></span>

                        <span class="text-gray-500 font-medium">Email</span>
                        <span class="text-gray-500 font-medium">:</span>
                        <span class="font-bold text-gray-900" x-text="selectedAdmin.email"></span>

                        <span class="text-gray-500 font-medium">Status</span>
                        <span class="text-gray-500 font-medium">:</span>
                        <div>
                            <template x-if="selectedAdmin.status_akun === 'aktif'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-[#DCFCE7] text-[#16A34A]">
                                    <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span> Aktif
                                </span>
                            </template>
                            <template x-if="selectedAdmin.status_akun !== 'aktif'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-[#FEE2E2] text-[#DC2626]">
                                    <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span> Nonaktif
                                </span>
                            </template>
                        </div>

                        <span class="text-gray-500 font-medium">Hak akses</span>
                        <span class="text-gray-500 font-medium">:</span>
                        <span class="font-bold text-gray-900">Admin FST</span>
                    </div>
                </div>

                <!-- Sisi Kanan: Tombol Edit Admin (Sesuai Mockup) -->
                <div class="shrink-0 self-start md:self-center">
                    <button type="button" 
                            @click="openEditModal(selectedAdmin)"
                            class="px-5 py-2.5 rounded-xl border border-[#2563EB] text-[#2563EB] hover:bg-blue-50/80 active:scale-95 font-bold text-xs sm:text-sm flex items-center gap-2 transition-all shadow-2xs cursor-pointer">
                        <i data-lucide="file-edit" class="w-4 h-4"></i>
                        <span>Edit Admin</span>
                    </button>
                </div>

            </div>
        </template>

        <!-- Kartu Daftar Admin & Tabel (Sesuai Mockup) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6 lg:p-7">
            
            <!-- Header Kartu: Judul & Kotak Pencarian -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-100">
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                    Daftar Admin
                </h2>

                <!-- Fitur Pencarian Nama atau Email Sesuai Mockup -->
                <form method="GET" action="{{ route('super_admin.pengguna.index') }}" class="relative w-full sm:w-72">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                    <input type="text" name="cari" value="{{ request('cari') }}" 
                           placeholder="Cari nama atau email" 
                           class="w-full pl-10 pr-4 py-2 sm:py-2.5 bg-white border border-gray-200/90 rounded-xl sm:rounded-2xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all shadow-2xs">
                </form>
            </div>

            <!-- Tabel Daftar Admin -->
            <div class="overflow-x-auto rounded-xl sm:rounded-2xl border border-gray-100 mt-5">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-[#E2E8F0]/40 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-center w-12">No</th>
                            <th scope="col" class="px-4 py-3.5">Nama Admin</th>
                            <th scope="col" class="px-4 py-3.5">Email</th>
                            <th scope="col" class="px-4 py-3.5 text-center">Status</th>
                            <th scope="col" class="px-4 py-3.5">Hak Akses</th>
                            <th scope="col" class="px-4 py-3.5 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($adminList as $index => $admin)
                        <tr class="hover:bg-gray-50/80 transition-colors cursor-pointer group"
                            :class="selectedAdmin && selectedAdmin.id === {{ $admin->id }} ? 'bg-blue-50/30' : ''"
                            @click="selectAdmin({{ json_encode($admin) }})">
                            <td class="px-4 py-3.5 text-center font-medium text-gray-600">
                                {{ $adminList->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3.5 font-medium text-gray-900 group-hover:text-[#801414] transition-colors">
                                {{ $admin->nama_lengkap }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-700">
                                {{ $admin->email }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($admin->status_akun === 'aktif')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#DCFCE7] text-[#16A34A]">
                                        <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FEE2E2] text-[#DC2626]">
                                        <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-medium text-gray-800">
                                Admin FST
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Tombol Edit -->
                                    <button type="button" 
                                            @click.stop="openEditModal({{ json_encode($admin) }})" 
                                            class="p-2 rounded-lg bg-[#DBEAFE] text-[#2563EB] hover:bg-blue-200 transition-colors cursor-pointer" 
                                            title="Edit Admin">
                                        <i data-lucide="file-edit" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button type="button" 
                                            @click.stop="openDeleteModal({{ json_encode($admin) }})" 
                                            class="p-2 rounded-lg bg-[#FEE2E2] text-[#EF4444] hover:bg-red-200 transition-colors cursor-pointer" 
                                            title="Hapus Admin">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                Tidak ada data admin yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($adminList->hasPages())
            <div class="pt-4">
                {{ $adminList->withQueryString()->links() }}
            </div>
            @endif

        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: MAHASISWA & DOSEN                  -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'pengguna_lain'" style="display: none;" class="space-y-6">
        
        <!-- Dosen Menunggu Verifikasi -->
        @if($dosenMenungguList->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-2xs">
            <h3 class="text-sm font-bold text-amber-900 mb-3 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                Persetujuan Akun Dosen ({{ $dosenMenungguList->count() }})
            </h3>
            <div class="divide-y divide-amber-100">
                @foreach($dosenMenungguList as $dosen)
                <div class="py-2.5 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $dosen->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $dosen->email }} • NIP: {{ $dosen->nomor_identitas }}</p>
                    </div>
                    <form method="POST" action="{{ route('super_admin.pengguna.setujui', $dosen->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors">
                            Setujui Akun
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Daftar Mahasiswa & Dosen Terdaftar -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-2xs p-5 sm:p-6">
            <h2 class="text-lg font-black text-gray-900 mb-4">Pengguna Terdaftar (Mahasiswa & Dosen)</h2>
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">NIM / NIP</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($mahasiswaList as $m)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $m->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $m->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $m->nomor_identitas }}</td>
                            <td class="px-4 py-3 text-gray-600 capitalize">Mahasiswa</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                            </td>
                        </tr>
                        @endforeach
                        @foreach($dosenList as $d)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $d->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $d->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $d->nomor_identitas }}</td>
                            <td class="px-4 py-3 text-gray-600 capitalize">Dosen</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: TAMBAH ADMIN BARU                 -->
    <!-- ========================================== -->
    <div x-show="showAddModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs" @click="showAddModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sm:p-8 overflow-hidden z-10"
                 @click.stop>
                
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#FEE2E2] flex items-center justify-center shrink-0">
                            <i data-lucide="user-plus" class="w-5 h-5 text-[#801414]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Tambah Admin Baru</h3>
                            <p class="text-xs text-gray-500 font-medium">Buat akun pengelola fasilitas FST baru</p>
                        </div>
                    </div>
                    <button type="button" @click="showAddModal = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('super_admin.pengguna.store') }}" class="space-y-4 pt-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required placeholder="Contoh: Ahmad Fauzan, S.T."
                               class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required placeholder="admin@gmail.com"
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">NIP / Nomor Identitas</label>
                            <input type="text" name="nomor_identitas" required placeholder="19850101..."
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" placeholder="08123456789"
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Status Akun</label>
                            <select name="status_akun" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kata Sandi</label>
                        <input type="password" name="kata_sandi" required minlength="8" placeholder="Minimal 8 karakter"
                               class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-[#801414] hover:bg-[#681010] text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm hover:shadow transition-all flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Simpan Admin</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: EDIT ADMIN                        -->
    <!-- ========================================== -->
    <div x-show="showEditModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs" @click="showEditModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sm:p-8 overflow-hidden z-10"
                 @click.stop>
                
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#DBEAFE] flex items-center justify-center shrink-0">
                            <i data-lucide="file-edit" class="w-5 h-5 text-[#2563EB]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Edit Data Admin</h3>
                            <p class="text-xs text-gray-500 font-medium">Perbarui profil dan status admin</p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form :action="'{{ url('super-admin/pengguna') }}/' + editForm.id" method="POST" class="space-y-4 pt-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" x-model="editForm.nama_lengkap" required
                               class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" x-model="editForm.email" required
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">NIP / Nomor Identitas</label>
                            <input type="text" name="nomor_identitas" x-model="editForm.nomor_identitas" required
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" x-model="editForm.nomor_telepon"
                                   class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Status Akun</label>
                            <select name="status_akun" x-model="editForm.status_akun" class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kata Sandi Baru (Opsional)</label>
                        <input type="password" name="kata_sandi" minlength="8" placeholder="Kosongkan jika tidak diubah"
                               class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#801414]/15 focus:border-[#801414] transition-all">
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-[#801414] hover:bg-[#681010] text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm hover:shadow transition-all flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Perbarui Admin</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: HAPUS ADMIN                       -->
    <!-- ========================================== -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs" @click="showDeleteModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sm:p-7 text-center z-10"
                 @click.stop>
                
                <div class="w-14 h-14 rounded-full bg-[#FEE2E2] text-[#EF4444] flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="trash-2" class="w-7 h-7"></i>
                </div>

                <h3 class="text-lg font-black text-gray-900 mb-1">Hapus Akun Admin?</h3>
                <p class="text-xs sm:text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus admin <span class="font-bold text-gray-900" x-text="deleteAdminData.nama_lengkap"></span>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form :action="'{{ url('super-admin/pengguna') }}/' + deleteAdminData.id" method="POST" class="flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm hover:shadow transition-all">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function kelolaAdminHandler(initialAdmin) {
    return {
        activeTab: 'admin',
        selectedAdmin: initialAdmin,
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editForm: {
            id: null,
            nama_lengkap: '',
            email: '',
            nomor_identitas: '',
            nomor_telepon: '',
            status_akun: 'aktif'
        },
        deleteAdminData: {
            id: null,
            nama_lengkap: ''
        },

        init() {
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        selectAdmin(admin) {
            this.selectedAdmin = admin;
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        openAddModal() {
            this.showAddModal = true;
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        openEditModal(admin) {
            this.editForm = {
                id: admin.id,
                nama_lengkap: admin.nama_lengkap,
                email: admin.email,
                nomor_identitas: admin.nomor_identitas,
                nomor_telepon: admin.nomor_telepon || '',
                status_akun: admin.status_akun || 'aktif'
            };
            this.showEditModal = true;
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        openDeleteModal(admin) {
            this.deleteAdminData = {
                id: admin.id,
                nama_lengkap: admin.nama_lengkap
            };
            this.showDeleteModal = true;
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        }
    };
}
</script>
@endsection
