<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            // 'mahasiswa', 'dosen', atau null (untuk admin/super_admin)
            $table->enum('jenis_pengguna', ['mahasiswa', 'dosen'])->nullable()->after('peran');
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn('jenis_pengguna');
        });
    }
};
