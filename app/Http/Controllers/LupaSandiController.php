<?php

namespace App\Http\Controllers;

use App\Mail\SendOtpMail;
use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LupaSandiController extends Controller
{
    public function showLupaSandi()
    {
        return view('auth.lupa_sandi');
    }

    public function kirimOtp(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $pengguna = Pengguna::where('email', $request->email)->first();

        if (!$pengguna) {
            return back()->withInput()->with('pesan_error', 'Email Anda belum terdaftar di sistem kami.');
        }

        // Generate 6 digit OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        // Hapus token lama jika ada
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Simpan token baru
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => $otp,
            'created_at' => Carbon::now()
        ]);

        // Kirim Email OTP
        try {
            Mail::to($request->email)->send(new SendOtpMail($otp));
        } catch (\Throwable $e) {
            // Jika gagal kirim email di server tertentu, catat error
        }

        // Simpan email dan timestamp pengiriman di session untuk countdown
        session([
            'email_reset' => $request->email,
            'otp_sent_at' => now()->timestamp,
        ]);

        $pesanSukses = 'Kode OTP 6 digit telah dikirimkan ke email Anda.';
        if (app()->isLocal() || config('app.debug')) {
            $pesanSukses .= ' (Mode Dev: Kode Anda adalah ' . $otp . ')';
        }

        return redirect()->route('lupa_sandi.verifikasi')->with('pesan_sukses', $pesanSukses);
    }

    public function kirimUlangOtp(Request $request)
    {
        $email = session('email_reset');

        if (!$email) {
            return redirect()->route('lupa_sandi')->with('pesan_error', 'Sesi verifikasi telah berakhir. Silakan masukkan email Anda kembali.');
        }

        $pengguna = Pengguna::where('email', $email)->first();
        if (!$pengguna) {
            return redirect()->route('lupa_sandi')->with('pesan_error', 'Akun tidak ditemukan.');
        }

        // Generate kode baru
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => $otp,
            'created_at' => Carbon::now()
        ]);

        try {
            Mail::to($email)->send(new SendOtpMail($otp));
        } catch (\Throwable $e) {
            // Abaikan error email jika testing
        }

        session(['otp_sent_at' => now()->timestamp]);

        $pesanSukses = 'Kode OTP baru telah berhasil dikirim ulang ke email Anda.';
        if (app()->isLocal() || config('app.debug')) {
            $pesanSukses .= ' (Mode Dev: Kode Baru adalah ' . $otp . ')';
        }

        return back()->with('pesan_sukses', $pesanSukses);
    }

    public function showVerifikasiOtp()
    {
        if (!session('email_reset')) {
            return redirect()->route('lupa_sandi');
        }
        return view('auth.verifikasi_otp');
    }

    public function verifikasiOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6'], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $email = session('email_reset');

        if (!$email) {
            return redirect()->route('lupa_sandi')->with('pesan_error', 'Sesi telah kedaluwarsa. Silakan mulai kembali.');
        }

        $tokenRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$tokenRecord || $tokenRecord->token !== $request->otp) {
            return back()->with('pesan_error', 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali.');
        }

        // Cek kedaluwarsa (15 menit)
        if (Carbon::parse($tokenRecord->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->with('pesan_error', 'Kode OTP telah kedaluwarsa (lebih dari 15 menit). Silakan klik Kirim Ulang OTP.');
        }

        session(['otp_verified' => true]);

        return redirect()->route('lupa_sandi.reset')->with('pesan_sukses', 'Verifikasi berhasil! Silakan buat kata sandi baru Anda.');
    }

    public function showResetSandi()
    {
        if (!session('email_reset') || !session('otp_verified')) {
            return redirect()->route('lupa_sandi');
        }
        return view('auth.reset_sandi');
    }

    public function resetSandi(Request $request)
    {
        $request->validate([
            'kata_sandi' => 'required|min:8|confirmed',
        ], [
            'kata_sandi.required'  => 'Kata sandi baru wajib diisi.',
            'kata_sandi.min'       => 'Kata sandi minimal 8 karakter.',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $email = session('email_reset');

        if (!$email || !session('otp_verified')) {
            return redirect()->route('lupa_sandi');
        }

        $pengguna = Pengguna::where('email', $email)->first();
        if ($pengguna) {
            $pengguna->update([
                'kata_sandi' => Hash::make($request->kata_sandi)
            ]);

            // Catat log aktivitas jika ada pengguna terkait
            try {
                LogAktivitas::create([
                    'pengguna_id'     => $pengguna->id,
                    'tindakan'        => 'reset_sandi',
                    'detail_kegiatan' => 'Reset kata sandi via verifikasi OTP email',
                    'alamat_ip'       => $request->ip(),
                    'created_at'      => now(),
                ]);
            } catch (\Throwable $e) {}
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        session()->forget(['email_reset', 'otp_verified', 'otp_sent_at']);

        return redirect()->route('masuk')->with('pesan_sukses', 'Kata sandi Anda berhasil diperbarui! Silakan masuk menggunakan kata sandi baru.');
    }
}

