<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique()->nullable();
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('nilai_barang', 15, 2)->default(0);
            $table->enum('status_ketersediaan', ['tersedia', 'dipinjam', 'tidak_tersedia'])
                  ->default('tersedia');
            $table->string('qr_code_string')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
