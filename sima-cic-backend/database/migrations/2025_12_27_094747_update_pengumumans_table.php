<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('pengumumans', function (Blueprint $table) {
            // Pastikan kolom ini ada untuk menampung nomor surat yang panjang
            if (!Schema::hasColumn('pengumumans', 'nomor_surat')) {
                $table->string('nomor_surat')->after('id')->nullable();
            }
            // Kolom untuk menyimpan path file PDF
            if (!Schema::hasColumn('pengumumans', 'file_path')) {
                $table->string('file_path')->after('isi')->nullable();
            }
        });
    }

    public function down() {
        Schema::table('pengumumans', function (Blueprint $table) {
            $table->dropColumn(['nomor_surat', 'file_path']);
        });
    }
};