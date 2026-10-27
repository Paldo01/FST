<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.masuk');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'kata_sandi' => 'required|min:6',
        ]);

        $pengguna = Pengguna::where('email', $request->email)->first();

        if ($pengguna && Hash::check($request->kata_sandi, $pengguna->kata_sandi)) {
            if ($pengguna->status_akun === 'menunggu') {
                return back()->withErrors([
                    'email' => 'Akun Anda masih dalam tahap verifikasi oleh Admin.',
                ])->onlyInput('email');
            }

            Auth::login($pengguna, $request->filled('ingat_saya'));

            LogAktivitas::catat('login', 'Pengguna berhasil masuk ke sistem');

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('pesan_sukses', 'Selamat datang, ' . $pengguna->nama_lengkap . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.daftar');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'jenis_pengguna' => 'required|in:mahasiswa,dosen',
            'email'          => 'required|email|unique:pengguna,email',
            'nomor_identitas'=> 'required|string|unique:pengguna,nomor_identitas',
            'nomor_telepon'  => 'nullable|string|max:20',
            'kata_sandi'     => 'required|min:8|confirmed',
        ], [
            'nomor_identitas.unique' => 'Akun dengan NIM ataupun NIP ini sudah terdaftar.',
            'email.unique'           => 'Akun dengan email ini sudah terdaftar.',
        ]);

        $status_akun = ($request->jenis_pengguna === 'dosen') ? 'menunggu' : 'aktif';

        $pengguna = Pengguna::create([
            'nama_lengkap'   => $request->nama_lengkap,
            'email'          => $request->email,
            'nomor_identitas'=> $request->nomor_identitas,
            'nomor_telepon'  => $request->nomor_telepon,
            'kata_sandi'     => $request->kata_sandi,
            'peran'          => 'pengguna',
            'jenis_pengguna' => $request->jenis_pengguna,
            'status_akun'    => $status_akun,
        ]);

        LogAktivitas::catat('registrasi', 'Pengguna baru mendaftar: ' . $pengguna->email);

        if ($status_akun === 'menunggu') {
            return redirect()->route('masuk')
                ->with('pesan_sukses', 'Registrasi berhasil! Akun Anda sedang menunggu konfirmasi Admin sebelum dapat digunakan.');
        }

        Auth::login($pengguna);

        return redirect()->route('dashboard')
            ->with('pesan_sukses', 'Registrasi berhasil! Selamat datang di SIMORA FST UINIB.');
    }

    public function logout(Request $request)
    {
        LogAktivitas::catat('logout', 'Pengguna keluar dari sistem');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')
            ->with('pesan_sukses', 'Anda telah berhasil keluar.');
    }
}
