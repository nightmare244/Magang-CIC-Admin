<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Kolom ini akan mencatat status akhir hari itu.
            // 'hadir' = absen via QR
            // 'sakit', 'izin', 'cuti' = disetujui admin
            $table->enum('status_hari', ['hadir', 'sakit', 'izin', 'cuti'])
                  ->default('hadir') // Defaultnya 'hadir' jika absen via QR
                  ->after('lokasi_pulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('status_hari');
        });
    }
};