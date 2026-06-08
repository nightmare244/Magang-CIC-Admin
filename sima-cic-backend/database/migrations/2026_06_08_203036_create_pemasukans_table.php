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
        Schema::create('pemasukans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemasukan');
            $table->string('tipe'); // tiket_masuk, donasi, sponsor, lainnya
            $table->integer('jumlah')->default(1);
            $table->decimal('nominal', 15, 2)->default(0);
            $table->date('tanggal_pemasukan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukans');
    }
};
