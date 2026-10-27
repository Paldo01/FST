@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('header', 'Profil Saya')
@section('description', 'Kelola informasi akun dan keamanan Anda.')

@section('content')
<div x-data="{ 
        activeTab: '{{ session('active_tab', '') }}' !== '' ? '{{ session('active_tab') }}' : (window.location.hash === '#ganti-sandi' ? 'sandi' : ({{ $errors->any() ? 'true' : 'false' }} ? 'sandi' : '')),
        init() {
            window.addEventListener('hashchange', () => {
                if(window.location.hash === '#ganti-sandi') this.activeTab = 'sandi';
            });
        }
     }" 
     class="transition-all duration-500 ease-in-out mx-auto flex flex-col md:flex-row gap-6"
     :class="activeTab !== '' ? 'max-w-4xl' : 'max-w-md'">
    
    <!-- Informasi Akun -->
    <div class="w-full transition-all duration-500" :class="activeTab !== '' ? 'md:w-1/3 shrink-0' : 'w-full'">
        <x-card>
            <div class="text-center mb-4">
                <div class="w-20 h-20 bg-marun-100 text-marun-800 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-3">
                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                </div>
                <h3 class="text-lg font-bold text-gray-900">{{ $user->nama_lengkap }}</h3>
                <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $user->peran)) }}</p>
            </div>
            
            <hr class="border-gray-100 my-4">
            
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Email</p>
                    <p class="text-sm font-medium text-gray-800">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">NIM / NIP / NIK</p>
                    <p class="text-sm font-medium text-gray-800">{{ $user->nomor_identitas }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                    <p class="text-sm font-medium text-gray-800">{{ $user->nomor_telepon }}</p>
                </div>
                @if($user->jenis_pengguna)
                <div>
                    <p class="text-xs text-gray-500 mb-1">Status Civitas</p>
                    <p class="text-sm font-medium text-gray-800">{{ ucfirst($user->jenis_pengguna) }}</p>
                </div>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center flex flex-col gap-2">
                <button type="button" @click="if(activeTab === 'sandi') { activeTab = ''; history.pushState('', document.title, window.location.pathname); } else { activeTab = 'sandi'; window.location.hash = '#ganti-sandi'; }" class="inline-flex justify-center items-center gap-2 text-sm text-marun-800 hover:text-mustard-600 font-medium transition-colors">
                    <i data-lucide="key" class="w-4 h-4"></i> <span x-text="activeTab === 'sandi' ? 'Tutup Ganti Sandi' : 'Ganti Kata Sandi'"></span>
                </button>
            </div>
        </x-card>
    </div>

    <!-- Right Side Cards Container -->
    <div class="w-full md:w-2/3 relative" x-show="activeTab !== ''" x-transition.opacity x-cloak>
        
        <!-- Form Ganti Kata Sandi -->
        <div id="ganti-sandi" x-show="activeTab === 'sandi'" 
             x-transition:enter="transition ease-out duration-500 delay-100"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="absolute inset-0">
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-marun-800"></i>
                    Keamanan & Kata Sandi
                </h3>

                <form action="{{ route('profil.sandi') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="sandi_lama" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Lama</label>
                        <input type="password" name="sandi_lama" id="sandi_lama" required
                            class="w-full rounded-lg border-gray-300 focus:border-mustard-500 focus:ring-mustard-500 text-sm">
                        @error('sandi_lama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="sandi_baru" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" name="sandi_baru" id="sandi_baru" required
                                class="w-full rounded-lg border-gray-300 focus:border-mustard-500 focus:ring-mustard-500 text-sm">
                            @error('sandi_baru')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sandi_baru_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Sandi Baru</label>
                            <input type="password" name="sandi_baru_confirmation" id="sandi_baru_confirmation" required
                                class="w-full rounded-lg border-gray-300 focus:border-mustard-500 focus:ring-mustard-500 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <button type="button" @click="activeTab = 'lupa_sandi'" class="text-sm text-marun-800 hover:text-mustard-600 font-medium">Lupa kata sandi?</button>
                        <button type="submit" class="px-5 py-2.5 bg-marun-800 text-white rounded-lg text-sm font-medium hover:bg-marun-900 transition-colors">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Form Lupa Kata Sandi (Reset via OTP) -->
        <div id="lupa-sandi" x-show="activeTab === 'lupa_sandi'"
             x-transition:enter="transition ease-out duration-500 delay-100"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="absolute inset-0">
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="mail-warning" class="w-5 h-5 text-marun-800"></i>
                    Reset Kata Sandi
                </h3>

                @if(!session('profil_otp_sent'))
                    <div class="bg-mustard-50 text-marun-900 p-4 rounded-lg text-sm mb-4">
                        Kami akan mengirimkan kode verifikasi (OTP) ke email <strong>{{ $user->email }}</strong>.
                        Pastikan email Anda aktif.
                    </div>
                    
                    <form action="{{ route('profil.otp.kirim') }}" method="POST">
                        @csrf
                        <div class="text-right">
                            <button type="submit" class="px-5 py-2.5 bg-marun-800 text-white rounded-lg text-sm font-medium hover:bg-marun-900 transition-colors inline-flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i> Kirim Kode OTP
                            </button>
                        </div>
                    </form>
                @elseif(!session('profil_otp_verified'))
                    <div class="bg-green-50 text-green-800 p-4 rounded-lg text-sm mb-4">
                        Kode OTP telah dikirim ke email <strong>{{ $user->email }}</strong>. Masukkan kode tersebut di bawah ini untuk verifikasi.
                    </div>

                    <form action="{{ route('profil.otp.verifikasi') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="otp" class="block text-sm font-medium text-gray-700 mb-1">Kode OTP</label>
                            <input type="text" name="otp" id="otp" required maxlength="6"
                                class="w-full rounded-lg border-gray-300 focus:border-mustard-500 focus:ring-mustard-500 text-sm tracking-widest font-mono">
                            @error('otp')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <button type="submit" form="kirim_ulang_otp" class="text-sm text-marun-800 hover:text-mustard-600 font-medium">Kirim ulang kode OTP</button>
                            <button type="submit" class="px-5 py-2.5 bg-marun-800 text-white rounded-lg text-sm font-medium hover:bg-marun-900 transition-colors">
                                Verifikasi OTP
                            </button>
                        </div>
                    </form>

                    <form action="{{ route('profil.otp.kirim') }}" method="POST" id="kirim_ulang_otp" class="hidden">
                        @csrf
                    </form>
                @else
                    <div class="bg-green-50 text-green-800 p-4 rounded-lg text-sm mb-4">
                        Verifikasi OTP berhasil! Silakan masukkan kata sandi baru Anda di bawah ini.
                    </div>

                    <form action="{{ route('profil.otp.reset') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="sandi_baru_otp" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                                <input type="password" name="sandi_baru" id="sandi_baru_otp" required
                                    class="w-full rounded-lg border-gray-300 focus:border-mustard-500 focus:ring-mustard-500 text-sm">
                                @error('sandi_baru')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="sandi_baru_otp_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Sandi Baru</label>
                                <input type="password" name="sandi_baru_confirmation" id="sandi_baru_otp_confirmation" required
                                    class="w-full rounded-lg border-gray-300 focus:border-mustard-500 focus:ring-mustard-500 text-sm">
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4">
                            <button type="submit" class="px-5 py-2.5 bg-marun-800 text-white rounded-lg text-sm font-medium hover:bg-marun-900 transition-colors">
                                Reset Kata Sandi
                            </button>
                        </div>
                    </form>
                @endif
            </x-card>
        </div>
    </div>

</div>
@endsection
