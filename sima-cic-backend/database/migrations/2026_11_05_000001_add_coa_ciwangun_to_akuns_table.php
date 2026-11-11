<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah 4 kolom CoA Ciwangun ke tabel akuns.
     * Kolom enum 'kategori' TIDAK diubah (tetap aset/kewajiban/ekuitas/pendapatan/beban).
     */
    public function up(): void
    {
        Schema::table('akuns', function (Blueprint $table) {
            // Nomor akun CoA (string persis: "AUTO--xxx" dua strip, "AUTO-xxx" satu strip)
            $table->string('nomor_akun', 50)->nullable()->unique()->after('kode_akun');
            // Deskripsi akun dari CoA
            $table->text('deskripsi')->nullable()->after('keterangan');
            // Nomor kategori (1,2,3,4,5,6,7,8,10,12,13,14,15,16,17)
            $table->unsignedTinyInteger('nomor_kategori')->nullable()->index()->after('deskripsi');
            // Nama kategori dari CoA
            $table->string('nama_kategori', 100)->nullable()->after('nomor_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akuns', function (Blueprint $table) {
            $table->dropColumn(['nomor_akun', 'deskripsi', 'nomor_kategori', 'nama_kategori']);
        });
    }
};

