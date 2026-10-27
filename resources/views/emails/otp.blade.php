<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP Reset Sandi</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-w-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .header { background-color: #800000; padding: 24px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .content { padding: 32px; text-align: center; color: #374151; }
        .content p { font-size: 16px; line-height: 1.5; margin-bottom: 24px; }
        .otp-box { background-color: #f3f4f6; border: 2px dashed #d1d5db; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .otp-code { font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #800000; margin: 0; }
        .footer { background-color: #f3f4f6; padding: 16px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SIMORA FST UINIB</h1>
        </div>
        <div class="content">
            <h2>Permintaan Reset Kata Sandi</h2>
            <p>Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda. Berikut adalah kode OTP Anda:</p>
            
            <div class="otp-box">
                <p class="otp-code">{{ $otp }}</p>
            </div>
            
            <p>Kode ini berlaku selama <strong>15 menit</strong>. Jangan bagikan kode ini kepada siapapun.</p>
            <p>Jika Anda tidak meminta reset kata sandi, abaikan email ini.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Sistem Informasi Peminjaman Sarana Prasarana FST UINIB.
        </div>
    </div>
</body>
</html>
