<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Fasilitas FST</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #800000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2, .header h3, .header p {
            margin: 2px 0;
        }
        .header h2 {
            color: #800000;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header h3 {
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #800000;
            color: white;
            font-size: 11px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .status-selesai { color: #166534; font-weight: bold; }
        .status-disetujui { color: #1e40af; font-weight: bold; }
        .status-ditolak { color: #991b1b; font-weight: bold; }
        
        .footer {
            margin-top: 50px;
            text-align: right;
            padding-right: 50px;
        }
        .footer p { margin: 5px 0; }
        .signature-space {
            height: 70px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Fakultas Sains dan Teknologi</h2>
        <h3>UIN Imam Bonjol Padang</h3>
        <p>Sistem Manajemen Peminjaman Ruang dan Alat Terpadu (SIMORA)</p>
    </div>

    <div class="info-section">
        <h3 style="text-align: center; text-decoration: underline; margin-bottom: 15px;">Laporan Data Peminjaman Fasilitas</h3>
        <table style="width: 50%; border: none;">
            <tr>
                <td style="border: none; padding: 2px; width: 120px;"><strong>Periode</strong></td>
                <td style="border: none; padding: 2px;">: {{ $tanggalAwal->format('d/m/Y') }} s.d {{ $tanggalAkhir->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;"><strong>Filter Status</strong></td>
                <td style="border: none; padding: 2px; text-transform: capitalize;">: {{ $statusFilter ?: 'Semua Status' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;"><strong>Total Data</strong></td>
                <td style="border: none; padding: 2px;">: {{ count($peminjaman) }} Pengajuan</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">Peminjam</th>
                <th style="width: 15%;">Fasilitas</th>
                <th style="width: 20%;">Tujuan Kegiatan</th>
                <th style="width: 22%;">Jadwal Pelaksanaan</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Kondisi Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $p->pengguna->nama_lengkap }}</strong><br>
                        <span style="font-size: 10px; color: #666;">{{ $p->pengguna->nomor_identitas }}</span>
                    </td>
                    <td>
                        {{ $p->fasilitas->nama_fasilitas }}<br>
                        <span style="font-size: 10px; color: #666;">({{ $p->fasilitas->kode_fasilitas }})</span>
                    </td>
                    <td>{{ Str::limit($p->tujuan_peminjaman, 50) }}</td>
                    <td style="font-size: 11px;">
                        {{ $p->tanggal_mulai->format('d/m/Y H:i') }} - <br>
                        {{ $p->tanggal_selesai->format('d/m/Y H:i') }}
                    </td>
                    <td class="text-center text-transform: capitalize;">
                        <span class="status-{{ $p->status_pengajuan }}">
                            {{ str_replace('_', ' ', $p->status_pengajuan) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($p->pengembalian)
                            <span style="text-transform: capitalize;">{{ $p->pengembalian->kondisi_akhir }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data peminjaman pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Padang, {{ now()->format('d F Y') }}</p>
        <p style="margin-bottom: 10px;">Pengelola Fasilitas,</p>
        <div class="signature-space"></div>
        <p style="text-decoration: underline; font-weight: bold;">{{ auth()->user()->nama_lengkap }}</p>
        <p>NIP. {{ auth()->user()->nomor_identitas }}</p>
    </div>

</body>
</html>
