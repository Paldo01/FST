<?php

namespace App\Mail;

use App\Models\Pengaturan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendRegisterOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $nama;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $nama = '')
    {
        $this->otp = $otp;
        $this->nama = $nama;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = Pengaturan::get('nama_website', 'SIMORA FST');
        return new Envelope(
            subject: 'Kode Verifikasi Pendaftaran Akun - ' . $appName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.register_otp',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
