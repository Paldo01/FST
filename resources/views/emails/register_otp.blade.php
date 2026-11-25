<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pendaftaran Akun - {{ \App\Models\Pengaturan::get('nama_website', 'SIMORA FST') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #1e293b;
        }
        .container {
            max-width: 520px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #801414 0%, #550c0c 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            color: #fce4e4;
            opacity: 0.9;
        }
        .content {
            padding: 36px 32px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .description {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 28px;
        }
        .otp-box {
            background-color: #fdf2f2;
            border: 2px dashed #f3a8a8;
            border-radius: 16px;
            padding: 20px;
            margin: 0 auto 28px;
            display: inline-block;
            min-width: 240px;
        }
        .otp-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #801414;
            margin-bottom: 6px;
        }
        .otp-code {
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 38px;
            font-weight: 900;
            letter-spacing: 10px;
            color: #801414;
            margin: 0;
            line-height: 1;
        }
        .warning-box {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 12px;
            color: #065f46;
            line-height: 1.5;
            margin-bottom: 20px;
            text-align: left;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Branding -->
        <div class="header">
            @php
                $namaWeb = \App\Models\Pengaturan::get('nama_website', 'SIMORA FST');
                $subNamaWeb = \App\Models\Pengaturan::get('sub_nama_website', 'Fakultas Sains dan Teknologi');
                $logoFile = \App\Models\Pengaturan::logoFilePath();
            @endphp
            @if(isset($message) && file_exists($logoFile))
                <div style="margin-bottom: 12px;">
                    <img src="{{ $message->embed($logoFile) }}" alt="{{ $namaWeb }}" style="height: 52px; max-height: 52px; width: auto; object-fit: contain; display: inline-block;">
                </div>
            @else
                <div style="margin-bottom: 12px;">
                    <img src="{{ url(\App\Models\Pengaturan::logoUrl()) }}" alt="{{ $namaWeb }}" style="height: 52px; max-height: 52px; width: auto; object-fit: contain; display: inline-block;">
                </div>
            @endif
            <h1>{{ $namaWeb }}</h1>
            <p>{{ $subNamaWeb }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 class="greeting">Selamat Datang, {{ $nama ?: 'Pengguna Baru' }}!</h2>
            <p class="description">
                Terima kasih telah mendaftar di sistem peminjaman sarana dan prasarana FST. Untuk memastikan email Anda asli dan mengaktifkan akun, silakan masukkan kode verifikasi (OTP) berikut:
            </p>
            
            <div class="otp-box">
                <div class="otp-label">Kode Verifikasi Pendaftaran</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="warning-box">
                <strong>Catatan Keamanan:</strong> Kode ini berlaku selama <strong>5 menit</strong>. Jangan bagikan kode ini kepada siapapun untuk menjaga keamanan akun Anda.
            </div>

            <p style="font-size: 12px; color: #64748b; margin: 0;">
                Jika Anda tidak merasa mendaftar di akun ini, silakan abaikan email ini.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ \App\Models\Pengaturan::get('nama_website', 'SIMORA FST') }} - {{ \App\Models\Pengaturan::get('alamat', 'Kampus III UIN Imam Bonjol Padang') }}.
        </div>
    </div>
</body>
</html>
