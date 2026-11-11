<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom quantity ke tabel pemasukans dan pengeluarans.
     * Bersifat non-destruktif, nullable, dengan default 1.00, data lama tidak terpengaruh.
     */
    public function up(): void
    {
        Schema::table('pemasukans', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->default(1.00)->nullable()->after('jumlah');
        });

        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->default(1.00)->nullable()->after('nominal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemasukans', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};

