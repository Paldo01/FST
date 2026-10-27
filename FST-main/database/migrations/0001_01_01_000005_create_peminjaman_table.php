<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->cascadeOnDelete();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->text('tujuan_peminjaman');
            $table->enum('status_pengajuan', ['menunggu', 'disetujui', 'ditolak', 'selesai'])->default('menunggu');
            $table->text('alasan_penolakan')->nullable();
            $table->string('berkas_persyaratan')->nullable()->comment('Path file PDF');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
