<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Kita ubah kolom status_masuk menjadi string dengan panjang 50 karakter
            // agar muat menampung 'TERLAMBAT (ALPA)'
            $table->string('status_masuk', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Kembalikan ke ukuran semula (misal 10) jika di-rollback
            $table->string('status_masuk', 10)->nullable()->change();
        });
    }
};