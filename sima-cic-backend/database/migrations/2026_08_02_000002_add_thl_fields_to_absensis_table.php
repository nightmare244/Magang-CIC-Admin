<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom untuk mendukung absensi THL oleh mandor/karyawan.
     * - input_by: FK ke users yang menginput (nullable = self-absen)
     * - metode:   'self' (scan sendiri) atau 'diinput_mandor' (diinput karyawan lain)
     * - keterangan: catatan tambahan (nullable)
     * - status:   enum status kehadiran untuk laporan THL
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Siapa yang menginput absensi ini
            $table->unsignedBigInteger('input_by')
                  ->nullable()
                  ->after('user_id')
                  ->comment('FK users: yang menginput absensi. NULL = self-absen');

            $table->foreign('input_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Metode absensi
            $table->enum('metode', ['self', 'diinput_mandor'])
                  ->default('self')
                  ->after('input_by')
                  ->comment('Cara absen: self=mandiri, diinput_mandor=diinput oleh karyawan lain');

            // Status kehadiran
            $table->enum('status_absen', ['hadir', 'izin', 'sakit', 'alpha'])
                  ->default('hadir')
                  ->after('metode')
                  ->comment('Status kehadiran untuk laporan THL');

            // Keterangan opsional
            $table->text('keterangan')
                  ->nullable()
                  ->after('status_absen')
                  ->comment('Catatan tambahan dari mandor atau karyawan');
        });
    }

    /**
     * Rollback.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['input_by']);
            $table->dropColumn(['input_by', 'metode', 'status_absen', 'keterangan']);
        });
    }
};
