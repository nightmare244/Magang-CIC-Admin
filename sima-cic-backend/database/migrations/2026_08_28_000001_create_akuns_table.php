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
        Schema::create('akuns', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun')->unique(); // e.g. 1-10001, 4-10001, 5-10001
            $table->string('nama_akun');
            $table->enum('kategori', ['aset', 'kewajiban', 'ekuitas', 'pendapatan', 'beban']);
            $table->enum('saldo_normal', ['debit', 'kredit'])->default('debit');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akuns');
    }
};
