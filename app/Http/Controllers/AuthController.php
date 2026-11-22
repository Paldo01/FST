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

        // Simpan data pendaftaran sementara di session (BELUM masuk ke database)
        // Data baru akan disimpan ke database setelah kode OTP berhasil diverifikasi
        session([
            'pending_registration' => [
                'nama_lengkap'      => $request->nama_lengkap,
                'email'             => $request->email,
                'nomor_identitas'   => $request->nomor_identitas,
                'nomor_telepon'     => $request->nomor_telepon,
                'kata_sandi'        => $request->kata_sandi,
                'peran'             => 'pengguna',
                'jenis_pengguna'    => $request->jenis_pengguna,
                'status_akun'       => $status_akun,
            ],
            'register_email'       => $request->email,
            'register_otp_sent_at' => now()->timestamp,
        ]);

        // Generate kode OTP pendaftaran
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => $otp,
            'created_at' => Carbon::now()
        ]);

        // Kirim email OTP
        try {
            Mail::to($request->email)->send(new SendRegisterOtpMail($otp, $request->nama_lengkap));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim OTP registrasi ke ' . $request->email . ' (OTP: ' . $otp . '): ' . $e->getMessage());
        }

        return redirect()->route('daftar.verifikasi')
            ->with('pesan_sukses', 'Kode OTP telah dikirim ke email Anda! Silakan masukkan 6 digit kode OTP untuk menyelesaikan pendaftaran.');
    }

    public function showVerifikasiPendaftaran()
    {
        if (!session('pending_registration') && !session('register_email')) {
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

        $pending = session('pending_registration');
        $email = session('register_email') ?? ($pending['email'] ?? null);

        if (!$email || !$pending) {
            return redirect()->route('daftar')->with('pesan_error', 'Sesi pendaftaran telah kedaluwarsa. Silakan mendaftar kembali.');
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

        // Pastikan email atau nomor identitas belum terdaftar di DB saat proses berlangsung
        if (Pengguna::where('email', $pending['email'])->exists()) {
            session()->forget(['pending_registration', 'register_email', 'register_otp_sent_at']);
            return redirect()->route('masuk')->with('pesan_error', 'Email ini sudah terdaftar. Silakan langsung masuk.');
        }

        if (Pengguna::where('nomor_identitas', $pending['nomor_identitas'])->exists()) {
            session()->forget(['pending_registration', 'register_email', 'register_otp_sent_at']);
            return redirect()->route('daftar')->with('pesan_error', 'NIM/NIP ini sudah terdaftar.');
        }

        // BARU SIMPAN KE DATABASE SETELAH OTP TERBUKTI BENAR
        $pengguna = Pengguna::create([
            'nama_lengkap'      => $pending['nama_lengkap'],
            'email'             => $pending['email'],
            'email_verified_at' => now(), // Langsung terverifikasi
            'nomor_identitas'   => $pending['nomor_identitas'],
            'nomor_telepon'     => $pending['nomor_telepon'],
            'kata_sandi'        => $pending['kata_sandi'],
            'peran'             => 'pengguna',
            'jenis_pengguna'    => $pending['jenis_pengguna'],
            'status_akun'       => $pending['status_akun'],
        ]);

        // Hapus token dan sesi registrasi
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['pending_registration', 'register_email', 'register_otp_sent_at']);

        LogAktivitas::catat('registrasi', 'Pengguna baru berhasil mendaftar dan memverifikasi email: ' . $pengguna->email);

        // Jika dosen: menunggu persetujuan admin
        if ($pengguna->status_akun === 'menunggu') {
            return redirect()->route('masuk')
                ->with('pesan_sukses', 'Email Anda berhasil diverifikasi dan akun berhasil dibuat! Akun Dosen Anda saat ini sedang menunggu konfirmasi/persetujuan Admin sebelum dapat digunakan.');
        }

        // Jika mahasiswa: langsung login otomatis
        Auth::login($pengguna);

        return redirect()->route('dashboard')
            ->with('pesan_sukses', 'Selamat datang, ' . $pengguna->nama_lengkap . '! Akun Anda telah aktif.');
    }

    public function kirimUlangOtpPendaftaran(Request $request)
    {
        $pending = session('pending_registration');
        $email = session('register_email') ?? ($pending['email'] ?? null);

        if (!$email || !$pending) {
            return redirect()->route('daftar')->with('pesan_error', 'Sesi pendaftaran telah berakhir. Silakan mendaftar kembali.');
        }

        $namaLengkap = $pending['nama_lengkap'] ?? 'Pengguna';

        $otp = sprintf("%06d", mt_rand(100000, 999999));
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => $otp,
            'created_at' => Carbon::now()
        ]);

        try {
            Mail::to($email)->send(new SendRegisterOtpMail($otp, $namaLengkap));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim ulang OTP registrasi ke ' . $email . ' (OTP: ' . $otp . '): ' . $e->getMessage());
        }

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
