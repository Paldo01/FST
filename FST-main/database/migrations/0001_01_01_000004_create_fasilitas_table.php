<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_fasilitas')->cascadeOnDelete();
            $table->string('kode_fasilitas')->unique();
            $table->string('nama_fasilitas');
            $table->enum('status', ['tersedia', 'dipinjam', 'perbaikan'])->default('tersedia');
            $table->string('kondisi_awal')->default('baik');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};
