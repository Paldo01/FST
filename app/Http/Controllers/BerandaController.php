<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\KategoriFasilitas;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BerandaController extends Controller
{
    public function index()
    {
        $kategoriList = KategoriFasilitas::withCount('fasilitas')->get();
        $totalRuangan = Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'ruangan'))->count();
        $totalAlat = Fasilitas::whereHas('kategori', fn($q) => $q->where('jenis', 'peralatan'))->count();
        $fasilitasTersedia = Fasilitas::where('status', 'tersedia')->count();
        $totalFasilitas = Fasilitas::count();
        $semuaFasilitas = Fasilitas::with('kategori')->orderBy('nama_fasilitas')->get();
        
        $fasilitasRuangan = $semuaFasilitas->where('kategori.jenis', 'ruangan')->groupBy('kategori.nama_kategori');
        $fasilitasAlat = $semuaFasilitas->where('kategori.jenis', 'peralatan')->groupBy('kategori.nama_kategori');

        return view('beranda', compact('kategoriList', 'fasilitasTersedia', 'totalFasilitas', 'totalRuangan', 'totalAlat', 'fasilitasRuangan', 'fasilitasAlat'));
    }

    public function katalogRuang(Request $request)
    {
        $cari = $request->input('cari');
        $kategoriAktif = $request->input('kategori');
        $tanggal = $request->input('tanggal');
        $urutkan = $request->input('urutkan', 'terbaru');

        $query = Fasilitas::with('kategori')
            ->whereHas('kategori', fn($q) => $q->where('jenis', 'ruangan'));
            
        if ($cari) {
            $query->where(function($q) use ($cari) {
                $q->where('nama_fasilitas', 'like', "%{$cari}%")
                  ->orWhere('deskripsi', 'like', "%{$cari}%")
                  ->orWhereHas('kategori', function($qk) use ($cari) {
                      $qk->where('nama_kategori', 'like', "%{$cari}%");
                  });
            });
        }

        if (!empty($kategoriAktif) && strtolower($kategoriAktif) !== 'semua') {
            $query->whereHas('kategori', function($q) use ($kategoriAktif) {
                $q->where('nama_kategori', $kategoriAktif)
                  ->orWhere('nama_kategori', 'like', "%{$kategoriAktif}%");
            });
        }

        // Sorting
        match ($urutkan) {
            'terlama' => $query->oldest(),
            'nama_asc' => $query->orderBy('nama_fasilitas', 'asc'),
            'nama_desc' => $query->orderBy('nama_fasilitas', 'desc'),
            default => $query->latest(),
        };

        $fasilitas = $query->get();

        $kategoriList = \App\Models\KategoriFasilitas::where('jenis', 'ruangan')
            ->whereHas('fasilitas')
            ->distinct()
            ->orderBy('nama_kategori')
            ->pluck('nama_kategori');
        
        // Peminjaman aktif pada tanggal yang dipilih (jika ada) atau hari ini
        $cekTanggal = $tanggal ? Carbon::parse($tanggal) : now();
        $dipinjamIds = Peminjaman::where('status_pengajuan', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $cekTanggal)
            ->whereDate('tanggal_selesai', '>=', $cekTanggal)
            ->pluck('fasilitas_id')
            ->toArray();

        return view('pengguna.katalog.ruang', compact(
            'fasilitas', 
            'dipinjamIds', 
            'kategoriList', 
            'kategoriAktif',
            'cari',
            'tanggal',
            'urutkan'
        ));
    }

    public function detailRuang(Request $request, Fasilitas $fasilitas)
    {
        $fasilitas->load('kategori');

        // Tanggal jadwal yang dipilih
        $tanggalInput = $request->input('tanggal');
        $selectedDate = $tanggalInput ? Carbon::parse($tanggalInput) : Carbon::create(2026, 9, 30);
        $tanggalFormatted = $selectedDate->translatedFormat('d F Y');

        // Peminjaman real dari database pada tanggal tersebut
        $peminjamanHariIni = Peminjaman::where('fasilitas_id', $fasilitas->id)
            ->whereIn('status_pengajuan', ['disetujui', 'selesai', 'menunggu'])
            ->whereDate('tanggal_mulai', '<=', $selectedDate)
            ->whereDate('tanggal_selesai', '>=', $selectedDate)
            ->get();

        // Siapkan slot waktu (Pagi: 08.00 - 12.00, Siang: 13.00 - 16.00)
        $jadwalSlots = [];
        
        // Cek peminjaman pagi
        $pagiBooking = $peminjamanHariIni->first(function($p) {
            $jamMulai = (int) $p->tanggal_mulai->format('H');
            return $jamMulai < 13;
        });

        // Cek peminjaman siang
        $siangBooking = $peminjamanHariIni->first(function($p) {
            $jamMulai = (int) $p->tanggal_mulai->format('H');
            return $jamMulai >= 12;
        });

        // Slot 1: 08.00 - 12.00
        if ($pagiBooking) {
            $jadwalSlots[] = [
                'tanggal' => $tanggalFormatted,
                'waktu' => '08.00 - 12.00',
                'keperluan' => $pagiBooking->tujuan_peminjaman,
                'status' => 'Terpakai',
            ];
        } else {
            // Default demo jika Aula dan tanggal default 30 Sept 2026
            if (str_contains(strtolower($fasilitas->nama_fasilitas), 'aula') && $selectedDate->format('Y-m-d') === '2026-09-30') {
                $jadwalSlots[] = [
                    'tanggal' => '30 September 2026',
                    'waktu' => '08.00 - 12.00',
                    'keperluan' => 'Seminar Sistem Informasi',
                    'status' => 'Terpakai',
                ];
            } else {
                $jadwalSlots[] = [
                    'tanggal' => $tanggalFormatted,
                    'waktu' => '08.00 - 12.00',
                    'keperluan' => '-',
                    'status' => 'Tersedia',
                ];
            }
        }

        // Slot 2: 13.00 - 16.00
        if ($siangBooking) {
            $jadwalSlots[] = [
                'tanggal' => $tanggalFormatted,
                'waktu' => '13.00 - 16.00',
                'keperluan' => $siangBooking->tujuan_peminjaman,
                'status' => 'Terpakai',
            ];
        } else {
            $jadwalSlots[] = [
                'tanggal' => $tanggalFormatted,
                'waktu' => '13.00 - 16.00',
                'keperluan' => '-',
                'status' => 'Tersedia',
            ];
        }

        // Foto & Gallery Thumbnails
        $namaLower = strtolower($fasilitas->nama_fasilitas);
        if (str_contains($namaLower, 'aula')) {
            $gallery = [
                asset('images/aula-fst.jpg'),
                asset('images/aula-fst-2.jpg'),
                asset('images/aula-fst-3.jpg'),
            ];
        } elseif (str_contains($namaLower, 'lab') || str_contains($namaLower, 'komputer')) {
            $gallery = [
                asset('images/lab-komputer.jpg'),
                asset('images/lab-komputer.jpg'),
                asset('images/lab-komputer.jpg'),
            ];
        } else {
            $gallery = [
                asset('images/ruang-kelas.jpg'),
                asset('images/ruang-kelas.jpg'),
                asset('images/ruang-kelas.jpg'),
            ];
        }

        $semuaRuangan = Fasilitas::with('kategori')
            ->whereHas('kategori', fn($q) => $q->where('jenis', 'ruangan'))
            ->where('status', 'tersedia')
            ->get()
            ->map(function($r) {
                $namaLower = strtolower($r->nama_fasilitas);
                if (str_contains($namaLower, 'aula')) {
                    $img = asset('images/aula-fst.jpg');
                } elseif (str_contains($namaLower, 'lab') || str_contains($namaLower, 'komputer')) {
                    $img = asset('images/lab-komputer.jpg');
                } else {
                    $img = asset('images/ruang-kelas.jpg');
                }
                return [
                    'id' => $r->id,
                    'nama' => $r->nama_fasilitas,
                    'kapasitas' => $r->kapasitas_formatted,
                    'kategori' => $r->kategori ? $r->kategori->nama_kategori : 'Ruang',
                    'fasilitas' => $r->deskripsi ?: 'Infocus, AC',
                    'lokasi' => $r->lokasi,
                    'gambar' => $img,
                ];
            });

        return view('pengguna.katalog.detail_ruang', compact(
            'fasilitas',
            'gallery',
            'jadwalSlots',
            'selectedDate',
            'tanggalFormatted',
            'semuaRuangan'
        ));
    }

    public function katalogAlat(Request $request)
    {
        $cari = $request->input('cari');
        $kategoriAktif = $request->input('kategori');
        $tanggal = $request->input('tanggal');
        $urutkan = $request->input('urutkan', 'terbaru');

        $query = Fasilitas::with('kategori')
            ->whereHas('kategori', fn($q) => $q->where('jenis', 'peralatan'));

        if ($cari) {
            $query->where(function($q) use ($cari) {
                $q->where('nama_fasilitas', 'like', "%{$cari}%")
                  ->orWhere('deskripsi', 'like', "%{$cari}%")
                  ->orWhereHas('kategori', function($qk) use ($cari) {
                      $qk->where('nama_kategori', 'like', "%{$cari}%");
                  });
            });
        }

        if (!empty($kategoriAktif) && strtolower($kategoriAktif) !== 'semua') {
            $query->whereHas('kategori', function($q) use ($kategoriAktif) {
                $q->where('nama_kategori', $kategoriAktif)
                  ->orWhere('nama_kategori', 'like', "%{$kategoriAktif}%");
            });
        }

        // Sorting
        match ($urutkan) {
            'terlama' => $query->oldest(),
            'nama_asc' => $query->orderBy('nama_fasilitas', 'asc'),
            'nama_desc' => $query->orderBy('nama_fasilitas', 'desc'),
            default => $query->orderByRaw("CASE 
                WHEN id = 17 THEN 1 
                WHEN id = 20 THEN 2 
                WHEN id = 19 THEN 3 
                ELSE 4 END")
                ->latest(),
        };

        $fasilitas = $query->get();

        $kategoriList = \App\Models\KategoriFasilitas::where('jenis', 'peralatan')
            ->whereHas('fasilitas')
            ->distinct()
            ->orderBy('nama_kategori')
            ->pluck('nama_kategori');

        // Peminjaman aktif pada tanggal yang dipilih (jika ada) atau hari ini
        $cekTanggal = $tanggal ? Carbon::parse($tanggal) : now();
        $dipinjamIds = Peminjaman::where('status_pengajuan', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $cekTanggal)
            ->whereDate('tanggal_selesai', '>=', $cekTanggal)
            ->pluck('fasilitas_id')
            ->toArray();

        return view('pengguna.katalog.alat', compact(
            'fasilitas', 
            'dipinjamIds', 
            'kategoriList', 
            'kategoriAktif',
            'cari',
            'tanggal',
            'urutkan'
        ));
    }

    /**
     * API: Data ketersediaan fasilitas untuk kalender interaktif.
     */
    public function ketersediaan(Request $request)
    {
        $peminjaman = Peminjaman::with(['fasilitas.kategori', 'pengguna'])
            ->whereIn('status_pengajuan', ['disetujui', 'menunggu'])
            ->when($request->start, fn($q) => $q->where('tanggal_selesai', '>=', $request->start))
            ->when($request->end, fn($q) => $q->where('tanggal_mulai', '<=', $request->end))
            ->when($request->fasilitas_id, fn($q) => $q->where('fasilitas_id', $request->fasilitas_id))
            ->when($request->kategori, function($q) use ($request) {
                $q->whereHas('fasilitas.kategori', function($q2) use ($request) {
                    $q2->where('nama_kategori', $request->kategori);
                });
            })
            ->get();

        $events = $peminjaman->map(function ($p) {
            $warna = match ($p->status_pengajuan) {
                'disetujui' => '#dc2626',
                'menunggu' => '#f59e0b',
                default => '#6b7280',
            };

            return [
                'id' => $p->id,
                'resourceId' => $p->fasilitas_id,
                'title' => $p->fasilitas->nama_fasilitas . ' - ' . $p->pengguna->nama_lengkap,
                'start' => $p->tanggal_mulai->toIso8601String(),
                'end' => $p->tanggal_selesai->toIso8601String(),
                'color' => $warna,
                'extendedProps' => [
                    'status' => $p->status_pengajuan,
                    'peminjam' => $p->pengguna->nama_lengkap,
                    'tujuan' => $p->tujuan_peminjaman,
                    'fasilitas' => $p->fasilitas->nama_fasilitas,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * API: Matriks timeline jadwal ruangan (Gantt chart per jam) untuk tanggal tertentu.
     */
    public function matriksJadwal(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $kategori = $request->input('kategori');
        $jenis = $request->input('jenis', 'ruangan');

        // Ambil fasilitas
        $queryFasilitas = Fasilitas::with('kategori')
            ->whereHas('kategori', function($q) use ($jenis, $kategori) {
                if ($jenis && $jenis !== 'semua') {
                    $q->where('jenis', $jenis);
                }
                if ($kategori && $kategori !== 'semua') {
                    $q->where('nama_kategori', $kategori);
                }
            })
            ->orderBy('nama_fasilitas');

        $fasilitas = $queryFasilitas->get();

        // Timeline 14 jam operasional: 07:00 hingga 21:00 (total 840 menit)
        $startHour = 7;
        $endHour = 21;
        $totalMinutes = ($endHour - $startHour) * 60; // 840 min

        $timelineStart = Carbon::parse($tanggal . ' 07:00:00');
        $timelineEnd = Carbon::parse($tanggal . ' 21:00:00');

        $slotJam = [
            ['mulai' => '07:00', 'selesai' => '08:00', 'label' => '07:00'],
            ['mulai' => '08:00', 'selesai' => '09:00', 'label' => '08:00'],
            ['mulai' => '09:00', 'selesai' => '10:00', 'label' => '09:00'],
            ['mulai' => '10:00', 'selesai' => '11:00', 'label' => '10:00'],
            ['mulai' => '11:00', 'selesai' => '12:00', 'label' => '11:00'],
            ['mulai' => '12:00', 'selesai' => '13:00', 'label' => '12:00'],
            ['mulai' => '13:00', 'selesai' => '14:00', 'label' => '13:00'],
            ['mulai' => '14:00', 'selesai' => '15:00', 'label' => '14:00'],
            ['mulai' => '15:00', 'selesai' => '16:00', 'label' => '15:00'],
            ['mulai' => '16:00', 'selesai' => '17:00', 'label' => '16:00'],
            ['mulai' => '17:00', 'selesai' => '18:00', 'label' => '17:00'],
            ['mulai' => '18:00', 'selesai' => '19:00', 'label' => '18:00'],
            ['mulai' => '19:00', 'selesai' => '20:00', 'label' => '19:00'],
            ['mulai' => '20:00', 'selesai' => '21:00', 'label' => '20:00'],
        ];

        $awalHari = $tanggal . ' 00:00:00';
        $akhirHari = $tanggal . ' 23:59:59';

        $semuaPeminjaman = Peminjaman::with(['pengguna', 'fasilitas'])
            ->whereIn('status_pengajuan', ['disetujui', 'menunggu'])
            ->where('tanggal_mulai', '<=', $akhirHari)
            ->where('tanggal_selesai', '>=', $awalHari)
            ->get();

        $ruanganTerpakaiCount = 0;

        $matriks = $fasilitas->map(function ($f) use ($semuaPeminjaman, $timelineStart, $timelineEnd, $totalMinutes, &$ruanganTerpakaiCount) {
            $peminjamanFasilitas = $semuaPeminjaman->where('fasilitas_id', $f->id)->sortBy('tanggal_mulai');

            if ($peminjamanFasilitas->isNotEmpty()) {
                $ruanganTerpakaiCount++;
            }

            $layers = [];
            $peminjamanList = [];

            foreach ($peminjamanFasilitas as $p) {
                $tStart = $p->tanggal_mulai;
                $tEnd = $p->tanggal_selesai;

                $effStart = $tStart->copy();
                if ($effStart < $timelineStart) {
                    $effStart = $timelineStart->copy();
                }
                $effEnd = $tEnd->copy();
                if ($effEnd > $timelineEnd) {
                    $effEnd = $timelineEnd->copy();
                }

                // Jika di luar jam operasional
                if ($effStart >= $timelineEnd || $effEnd <= $timelineStart) {
                    continue;
                }

                $startMin = $timelineStart->diffInMinutes($effStart);
                $durationMin = $effStart->diffInMinutes($effEnd);

                $leftPercent = round(($startMin / $totalMinutes) * 100, 2);
                $widthPercent = round(($durationMin / $totalMinutes) * 100, 2);

                if ($widthPercent < 2.5) {
                    $widthPercent = 2.5;
                }
                if ($leftPercent + $widthPercent > 100) {
                    $widthPercent = 100 - $leftPercent;
                }

                // Alokasikan layer/baris agar peminjaman yang tumpang tindih tidak saling menutupi
                $assignedLayer = null;
                foreach ($layers as $idx => $endMinLimit) {
                    if ($startMin >= $endMinLimit) {
                        $assignedLayer = $idx;
                        $layers[$idx] = $startMin + $durationMin;
                        break;
                    }
                }
                if ($assignedLayer === null) {
                    $assignedLayer = count($layers);
                    $layers[] = $startMin + $durationMin;
                }

                $labelWaktu = $tStart->format('H:i') . ' - ' . $tEnd->format('H:i');
                if ($tStart < $timelineStart) {
                    $labelWaktu = 'Lanjutan s/d ' . $tEnd->format('H:i');
                }
                if ($tEnd > $timelineEnd) {
                    $labelWaktu = $tStart->format('H:i') . ' s/d Selesai Hari Ini';
                }

                $peminjamanList[] = [
                    'id' => $p->id,
                    'peminjam' => $p->pengguna->nama_lengkap ?? 'Anonim',
                    'nomor_identitas' => $p->pengguna->nomor_identitas ?? '-',
                    'tujuan' => $p->tujuan_peminjaman,
                    'status' => $p->status_pengajuan,
                    'jam_mulai' => $tStart->format('H:i'),
                    'jam_selesai' => $tEnd->format('H:i'),
                    'label_waktu' => $labelWaktu,
                    'left_percent' => $leftPercent,
                    'width_percent' => $widthPercent,
                    'layer' => $assignedLayer,
                    'ruangan' => $f->nama_fasilitas,
                    'start_str' => $tStart->toIso8601String(),
                    'end_str' => $tEnd->toIso8601String(),
                ];
            }

            $layerCount = max(1, count($layers));
            $barHeight = $layerCount > 1 ? 34 : 40;
            $rowHeight = $layerCount > 1 ? ($layerCount * 42 + 14) : 60;

            foreach ($peminjamanList as &$item) {
                $item['top_px'] = 10 + ($item['layer'] * ($barHeight + 6));
                $item['height_px'] = $barHeight;
            }

            return [
                'id' => $f->id,
                'nama_fasilitas' => $f->nama_fasilitas,
                'kode_fasilitas' => $f->kode_fasilitas,
                'kategori' => $f->kategori->nama_kategori ?? '-',
                'jenis' => $f->kategori->jenis ?? 'ruangan',
                'status_fisik' => $f->status,
                'kondisi' => $f->kondisi_awal ?? 'baik',
                'deskripsi' => $f->deskripsi,
                'gambar' => $f->gambar ? \Illuminate\Support\Facades\Storage::url($f->gambar) : null,
                'peminjaman_list' => $peminjamanList,
                'has_booking' => count($peminjamanList) > 0,
                'tersedia_hari_ini' => count($peminjamanList) === 0 && $f->status !== 'perbaikan',
                'row_height_px' => $rowHeight,
            ];
        });

        // Hitung fasilitas dengan kondisi bermasalah (rusak ringan / perbaikan)
        $rusakCount = $fasilitas->filter(function($f) {
            return $f->status === 'perbaikan' || $f->kondisi_awal === 'rusak_ringan' || $f->kondisi_awal === 'rusak_berat';
        })->count();

        // Ambil kategori dinamis sesuai jenis fasilitas terpilih
        $kategoriList = KategoriFasilitas::where(function($q) use ($jenis) {
            if ($jenis && $jenis !== 'semua') {
                $q->where('jenis', $jenis);
            }
        })->pluck('nama_kategori')->toArray();

        Carbon::setLocale('id');
        $cDate = Carbon::parse($tanggal);

        return response()->json([
            'tanggal' => $tanggal,
            'tanggal_formatted' => $cDate->translatedFormat('l, d F Y'),
            'jenis' => $jenis,
            'kategori_aktif' => $kategori ?? 'semua',
            'kategori_list' => $kategoriList,
            'slot_jam' => $slotJam,
            'total_fasilitas' => $fasilitas->count(),
            'terpakai_hari_ini' => $ruanganTerpakaiCount,
            'kosong_hari_ini' => max(0, $fasilitas->count() - $ruanganTerpakaiCount),
            'rusak_hari_ini' => $rusakCount,
            'matriks' => $matriks,
        ]);
    }

    /**
     * API: Pencarian fasilitas (live search).
     */
    public function cariFasilitas(Request $request)
    {
        $fasilitas = Fasilitas::with('kategori')
            ->when($request->q, function ($query) use ($request) {
                $query->where('nama_fasilitas', 'like', "%{$request->q}%")
                    ->orWhere('kode_fasilitas', 'like', "%{$request->q}%");
            })
            ->when($request->jenis, function ($query) use ($request) {
                $query->whereHas('kategori', fn($q) => $q->where('jenis', $request->jenis));
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->limit(20)
            ->get();

        return response()->json($fasilitas);
    }
}
