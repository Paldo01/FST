<?php

namespace App\Http\Controllers;

use App\Mail\SendRegisterOtpMail;
use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            'email' => 'required',
            'kata_sandi' => 'required|min:6',
        ], [
            'email.required' => 'Email atau NIM wajib diisi.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $loginInput = $request->email;
        $pengguna = Pengguna::where('email', $loginInput)
            ->orWhere('nomor_identitas', $loginInput)
            ->first();

        if ($pengguna && Hash::check($request->kata_sandi, $pengguna->kata_sandi)) {
            // Cek verifikasi email via OTP
            if (!$pengguna->isEmailVerified()) {
                // Generate OTP baru
                $otp = sprintf("%06d", mt_rand(100000, 999999));
                DB::table('password_reset_tokens')->where('email', $pengguna->email)->delete();
                DB::table('password_reset_tokens')->insert([
                    'email'      => $pengguna->email,
                    'token'      => $otp,
                    'created_at' => Carbon::now()
                ]);

                try {
                    Mail::to($pengguna->email)->send(new SendRegisterOtpMail($otp, $pengguna->nama_lengkap));
                } catch (\Throwable $e) {}

                session([
                    'register_email'       => $pengguna->email,
                    'register_otp_sent_at' => now()->timestamp,
                ]);

                return redirect()->route('daftar.verifikasi')->with('pesan_error', 'Email akun Anda belum diverifikasi. Kami telah mengirimkan kode OTP baru ke email Anda.');
            }

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
            'email' => 'Email / NIM atau kata sandi tidak sesuai.',
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
            'nama_lengkap'    => 'required|string|max:255',
            'jenis_pengguna'  => 'required|in:mahasiswa,dosen',
            'email'           => 'required|email|unique:pengguna,email',
            'nomor_identitas' => 'required|string|unique:pengguna,nomor_identitas',
            'nomor_telepon'   => 'nullable|string|max:20',
            'kata_sandi'      => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
                'regex:/[^a-zA-Z0-9]/',
            ],
        ], [
            'nomor_identitas.unique' => 'Akun dengan NIM ataupun NIP ini sudah terdaftar.',
            'email.unique'           => 'Akun dengan email ini sudah terdaftar.',
            'kata_sandi.required'    => 'Kata sandi wajib diisi.',
            'kata_sandi.min'         => 'Kata sandi minimal 8 karakter.',
            'kata_sandi.confirmed'   => 'Konfirmasi kata sandi tidak cocok.',
            'kata_sandi.regex'       => 'Kata sandi harus merupakan kombinasi tulisan/huruf, angka, dan karakter/simbol khusus (contoh: Rahasia_2026 atau Akun123!).',
        ]);

        $status_akun = ($request->jenis_pengguna === 'dosen') ? 'menunggu' : 'aktif';

        $pengguna = Pengguna::create([
            'nama_lengkap'      => $request->nama_lengkap,
            'email'             => $request->email,
            'email_verified_at' => null, // Belum terverifikasi sampai OTP dimasukkan
            'nomor_identitas'   => $request->nomor_identitas,
            'nomor_telepon'     => $request->nomor_telepon,
            'kata_sandi'        => $request->kata_sandi,
            'peran'             => 'pengguna',
            'jenis_pengguna'    => $request->jenis_pengguna,
            'status_akun'       => $status_akun,
        ]);

        LogAktivitas::catat('registrasi', 'Pengguna baru mendaftar: ' . $pengguna->email);

        // Generate kode OTP pendaftaran
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        DB::table('password_reset_tokens')->where('email', $pengguna->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $pengguna->email,
            'token'      => $otp,
            'created_at' => Carbon::now()
        ]);

        // Kirim email OTP
        try {
            Mail::to($pengguna->email)->send(new SendRegisterOtpMail($otp, $pengguna->nama_lengkap));
        } catch (\Throwable $e) {}

        session([
            'register_email'       => $pengguna->email,
            'register_otp_sent_at' => now()->timestamp,
        ]);

        return redirect()->route('daftar.verifikasi')
            ->with('pesan_sukses', 'Pendaftaran berhasil! Silakan masukkan 6 digit kode OTP yang telah dikirim ke email Anda untuk mengaktifkan akun.');
    }

    public function showVerifikasiPendaftaran()
    {
        if (!session('register_email')) {
            return redirect()->route('daftar');
        }
        return view('auth.verifikasi_pendaftaran');
    }

    public function verifikasiPendaftaran(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6'], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $email = session('register_email');

        if (!$email) {
            return redirect()->route('daftar')->with('pesan_error', 'Sesi verifikasi telah kedaluwarsa. Silakan mendaftar kembali.');
        }

        $tokenRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$tokenRecord || $tokenRecord->token !== $request->otp) {
            return back()->with('pesan_error', 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali email Anda.');
        }

        // Cek kedaluwarsa (5 menit)
        if (Carbon::parse($tokenRecord->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->with('pesan_error', 'Kode OTP telah kedaluwarsa (lebih dari 5 menit). Silakan klik Kirim Ulang Kode OTP.');
        }

        $pengguna = Pengguna::where('email', $email)->first();

        if (!$pengguna) {
            return redirect()->route('daftar')->with('pesan_error', 'Akun tidak ditemukan.');
        }

        // Aktifkan verifikasi email
        $pengguna->email_verified_at = now();
        $pengguna->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['register_email', 'register_otp_sent_at']);

        LogAktivitas::catat('verifikasi_email', 'Pengguna berhasil memverifikasi email pendaftaran: ' . $pengguna->email);

        // Jika dosen: menunggu persetujuan admin
        if ($pengguna->status_akun === 'menunggu') {
            return redirect()->route('masuk')
                ->with('pesan_sukses', 'Email Anda berhasil diverifikasi! Akun Dosen Anda saat ini sedang menunggu konfirmasi/persetujuan Admin sebelum dapat digunakan.');
        }

        // Jika mahasiswa: langsung login otomatis
        Auth::login($pengguna);

        return redirect()->route('dashboard')
            ->with('pesan_sukses', 'Selamat datang, ' . $pengguna->nama_lengkap . '! Akun Anda telah berhasil diverifikasi dan aktif.');
    }

    public function kirimUlangOtpPendaftaran(Request $request)
    {
        $email = session('register_email');

        if (!$email) {
            return redirect()->route('daftar')->with('pesan_error', 'Sesi verifikasi telah berakhir. Silakan mendaftar kembali.');
        }

        $pengguna = Pengguna::where('email', $email)->first();
        if (!$pengguna) {
            return redirect()->route('daftar')->with('pesan_error', 'Akun tidak ditemukan.');
        }

        $otp = sprintf("%06d", mt_rand(100000, 999999));
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => $otp,
            'created_at' => Carbon::now()
        ]);

        try {
            Mail::to($email)->send(new SendRegisterOtpMail($otp, $pengguna->nama_lengkap));
        } catch (\Throwable $e) {}

        session(['register_otp_sent_at' => now()->timestamp]);

        return back()->with('pesan_sukses', 'Kode OTP baru telah berhasil dikirimkan ke email Anda.');
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
