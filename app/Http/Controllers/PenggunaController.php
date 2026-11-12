<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * Super Admin: Kelola akun admin.
     */
    public function index(Request $request)
    {
        $cari = $request->cari;

        $adminList = Pengguna::where('peran', 'admin')
            ->when($cari, fn($q) => $q->where(fn($q2) =>
                $q2->where('nama_lengkap', 'like', "%$cari%")
                   ->orWhere('email', 'like', "%$cari%")
                   ->orWhere('nomor_identitas', 'like', "%$cari%")
            ))
            ->orderByRaw("CASE WHEN email = 'ahmad@gmail.com' THEN 1 WHEN email = 'siti@gmail.com' THEN 2 WHEN email = 'marisa@gmail.com' THEN 3 WHEN email = 'rina@gmail.com' THEN 4 ELSE 5 END")
            ->paginate(10, ['*'], 'admin_page');

        $mahasiswaList = Pengguna::where('peran', 'pengguna')
            ->where('jenis_pengguna', 'mahasiswa')
            ->when($cari, fn($q) => $q->where(fn($q2) =>
                $q2->where('nama_lengkap', 'like', "%$cari%")
                   ->orWhere('email', 'like', "%$cari%")
                   ->orWhere('nomor_identitas', 'like', "%$cari%")
            ))
            ->latest()
            ->paginate(10, ['*'], 'mahasiswa_page');

        $dosenList = Pengguna::where('peran', 'pengguna')
            ->where('jenis_pengguna', 'dosen')
            ->where('status_akun', 'aktif')
            ->when($cari, fn($q) => $q->where(fn($q2) =>
                $q2->where('nama_lengkap', 'like', "%$cari%")
                   ->orWhere('email', 'like', "%$cari%")
                   ->orWhere('nomor_identitas', 'like', "%$cari%")
            ))
            ->latest()
            ->paginate(10, ['*'], 'dosen_page');

        $dosenMenungguList = Pengguna::where('peran', 'pengguna')
            ->where('jenis_pengguna', 'dosen')
            ->where('status_akun', 'menunggu')
            ->when($cari, fn($q) => $q->where(fn($q2) =>
                $q2->where('nama_lengkap', 'like', "%$cari%")
                   ->orWhere('email', 'like', "%$cari%")
                   ->orWhere('nomor_identitas', 'like', "%$cari%")
            ))
            ->latest()
            ->paginate(10, ['*'], 'dosen_menunggu_page');

        $lainnyaList = Pengguna::where('peran', 'pengguna')
            ->whereNull('jenis_pengguna')
            ->when($cari, fn($q) => $q->where(fn($q2) =>
                $q2->where('nama_lengkap', 'like', "%$cari%")
                   ->orWhere('email', 'like', "%$cari%")
                   ->orWhere('nomor_identitas', 'like', "%$cari%")
            ))
            ->latest()
            ->paginate(10, ['*'], 'lainnya_page');

        // Admin default untuk highlight card
        $defaultAdmin = $adminList->firstWhere('email', 'ahmad@gmail.com') ?? $adminList->first();

        return view('super_admin.pengguna.index', compact('adminList', 'defaultAdmin', 'mahasiswaList', 'dosenList', 'dosenMenungguList', 'lainnyaList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'nomor_identitas' => 'required|string|unique:pengguna,nomor_identitas',
            'nomor_telepon' => 'nullable|string|max:20',
            'kata_sandi' => 'required|min:8',
            'status_akun' => 'nullable|in:aktif,nonaktif,menunggu',
        ]);

        $validated['peran'] = 'admin';
        if (empty($validated['status_akun'])) {
            $validated['status_akun'] = 'aktif';
        }

        Pengguna::create($validated);

        LogAktivitas::catat('tambah_admin', 'Menambahkan admin baru: ' . $validated['email']);

        return back()->with('pesan_sukses', 'Akun admin berhasil dibuat!');
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email,' . $pengguna->id,
            'nomor_identitas' => 'required|string|unique:pengguna,nomor_identitas,' . $pengguna->id,
            'nomor_telepon' => 'nullable|string|max:20',
            'kata_sandi' => 'nullable|min:8',
            'status_akun' => 'nullable|in:aktif,nonaktif,menunggu',
        ]);

        if (empty($validated['kata_sandi'])) {
            unset($validated['kata_sandi']);
        }

        $pengguna->update($validated);

        LogAktivitas::catat('ubah_admin', 'Mengubah data admin: ' . $pengguna->email);

        return back()->with('pesan_sukses', 'Data admin berhasil diperbarui!');
    }

    public function setujui($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->update(['status_akun' => 'aktif']);

        LogAktivitas::catat('konfirmasi_akun', 'Super Admin menyetujui akun dosen: ' . $pengguna->email);

        return back()->with('pesan_sukses', 'Akun dosen berhasil disetujui.');
    }

    public function destroy(Pengguna $pengguna)
    {
        if ($pengguna->isSuperAdmin()) {
            return back()->with('pesan_error', 'Tidak dapat menghapus akun Super Admin.');
        }

        $email = $pengguna->email;
        $pengguna->delete();

        LogAktivitas::catat('hapus_admin', 'Menghapus admin: ' . $email);

        return back()->with('pesan_sukses', 'Akun admin berhasil dihapus!');
    }

    /**
     * Super Admin: Audit Log.
     */
    public function logAktivitas(Request $request)
    {
        $cari = $request->get('cari');
        $rentangTanggal = $request->get('rentang_tanggal') ?? $request->get('tanggal');

        $query = LogAktivitas::with('pengguna')->latest('created_at');

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('tindakan', 'like', "%{$cari}%")
                    ->orWhere('detail_kegiatan', 'like', "%{$cari}%")
                    ->orWhereHas('pengguna', function ($qp) use ($cari) {
                        $qp->where('nama_lengkap', 'like', "%{$cari}%")
                           ->orWhere('email', 'like', "%{$cari}%")
                           ->orWhere('peran', 'like', "%{$cari}%");
                    });
            });
        }

        if ($rentangTanggal) {
            if (str_contains($rentangTanggal, ' to ')) {
                [$start, $end] = explode(' to ', $rentangTanggal, 2);
                $query->whereDate('created_at', '>=', trim($start))
                      ->whereDate('created_at', '<=', trim($end));
            } elseif (str_contains($rentangTanggal, ' - ')) {
                [$start, $end] = explode(' - ', $rentangTanggal, 2);
                $query->whereDate('created_at', '>=', trim($start))
                      ->whereDate('created_at', '<=', trim($end));
            } else {
                $query->whereDate('created_at', $rentangTanggal);
            }
        }

        $logs = $query->paginate(8)->withQueryString();

        return view('super_admin.log.index', compact('logs'));
    }
}
