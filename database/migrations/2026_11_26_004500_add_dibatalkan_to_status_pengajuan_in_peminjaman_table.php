<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_pengajuan ENUM('menunggu', 'disetujui', 'ditolak', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_pengajuan ENUM('menunggu', 'disetujui', 'ditolak', 'selesai') NOT NULL DEFAULT 'menunggu'");
    }
};
