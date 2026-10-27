<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Pengguna;
use App\Mail\SendOtpMail;
use Carbon\Carbon;

class LupaSandiController extends Controller
{
    public function showLupaSandi()
    {
        return view('auth.lupa_sandi');
    }

    public function kirimOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $pengguna = Pengguna::where('email', $request->email)->first();

        if (!$pengguna) {
            return back()->with('pesan_error', 'Email Anda belum terdaftar di sistem kami.');
        }

        // Generate 6 digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));

        // Delete old token if exists
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insert new OTP
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => Carbon::now()
        ]);

        // Send Email
        Mail::to($request->email)->send(new SendOtpMail($otp));

        // Store email in session for next step
        session(['email_reset' => $request->email]);

        return redirect()->route('lupa_sandi.verifikasi')->with('pesan_sukses', 'Kode OTP telah dikirim ke email Anda.');
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
        $request->validate(['otp' => 'required|digits:6']);
        $email = session('email_reset');

        if (!$email) {
            return redirect()->route('lupa_sandi');
        }

        $tokenRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$tokenRecord || $tokenRecord->token !== $request->otp) {
            return back()->with('pesan_error', 'Kode OTP yang Anda masukkan salah.');
        }

        // Check if expired (e.g. > 15 minutes)
        if (Carbon::parse($tokenRecord->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->with('pesan_error', 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.');
        }

        session(['otp_verified' => true]);

        return redirect()->route('lupa_sandi.reset');
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
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        session()->forget(['email_reset', 'otp_verified']);

        return redirect()->route('masuk')->with('pesan_sukses', 'Kata sandi berhasil diubah! Silakan masuk dengan kata sandi baru Anda.');
    }
}
