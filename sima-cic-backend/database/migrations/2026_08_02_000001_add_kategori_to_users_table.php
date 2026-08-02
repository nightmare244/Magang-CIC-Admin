<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom kategori dan can_absen_thl ke tabel users.
     * - kategori: membedakan karyawan tetap vs THL (Tenaga Harian Lepas)
     * - can_absen_thl: permission tambahan agar karyawan bisa input absensi THL
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('kategori', ['karyawan', 'thl'])
                  ->default('karyawan')
                  ->after('status_kerja')
                  ->comment('Kategori pegawai: karyawan tetap atau THL');

            $table->boolean('can_absen_thl')
                  ->default(false)
                  ->after('kategori')
                  ->comment('Izin input absensi untuk akun THL');
        });
    }

    /**
     * Rollback: hapus kolom yang ditambahkan.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'can_absen_thl']);
        });
    }
};
