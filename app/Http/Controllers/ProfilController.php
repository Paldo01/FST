<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\LogAktivitas;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profil.index', compact('user'));
    }

    public function updateSandi(Request $request)
    {
        $request->validate([
            'sandi_lama' => 'required',
            'sandi_baru' => 'required|min:8|confirmed',
        ], [
            'sandi_baru.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'sandi_baru.min' => 'Kata sandi baru minimal 8 karakter.'
        ]);

        $user = auth()->user();

        if (!Hash::check($request->sandi_lama, $user->kata_sandi)) {
            return back()->withErrors(['sandi_lama' => 'Kata sandi lama tidak sesuai.']);
        }

        $user->kata_sandi = Hash::make($request->sandi_baru);
        $user->save();

        LogAktivitas::create([
            'pengguna_id' => $user->id,
            'aktivitas' => 'Memperbarui kata sandi'
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function kirimOtpProfile(Request $request)
    {
        $user = auth()->user();
        $otp = sprintf("%06d", mt_rand(1, 999999));

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $otp,
            'created_at' => \Carbon\Carbon::now()
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SendOtpMail($otp));

        session(['profil_otp_sent' => true]);
        session()->forget('profil_otp_verified');

        return back()->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek email Anda.')->with('active_tab', 'lupa_sandi');
    }

    public function verifikasiOtpProfile(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $user = auth()->user();
        
        $tokenData = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('token', $request->otp)
            ->first();

        if (!$tokenData) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau salah.'])->with('active_tab', 'lupa_sandi');
        }

        // Check expiration (e.g. 15 minutes)
        if (\Carbon\Carbon::parse($tokenData->created_at)->addMinutes(15)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang.'])->with('active_tab', 'lupa_sandi');
        }

        session(['profil_otp_verified' => true]);
        return back()->with('success', 'Kode OTP valid. Silakan buat kata sandi baru Anda.')->with('active_tab', 'lupa_sandi');
    }

    public function resetSandiOtp(Request $request)
    {
        if (!session('profil_otp_verified')) {
            return back()->withErrors(['otp' => 'Silakan verifikasi OTP terlebih dahulu.'])->with('active_tab', 'lupa_sandi');
        }

        $request->validate([
            'sandi_baru' => 'required|min:8|confirmed',
        ], [
            'sandi_baru.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'sandi_baru.min' => 'Kata sandi baru minimal 8 karakter.'
        ]);

        $user = auth()->user();

        $user->kata_sandi = Hash::make($request->sandi_baru);
        $user->save();

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        session()->forget(['profil_otp_sent', 'profil_otp_verified']);

        LogAktivitas::create([
            'pengguna_id' => $user->id,
            'aktivitas' => 'Reset kata sandi via OTP di profil'
        ]);

        return back()->with('success', 'Kata sandi berhasil direset.');
    }
}
