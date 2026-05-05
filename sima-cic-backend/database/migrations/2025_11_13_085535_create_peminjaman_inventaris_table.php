<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_inventaris', function (Blueprint $table) {
            $table->id();

            // Pemohon
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Barang yang dipinjam
            $table->foreignId('inventaris_id')
                ->constrained('inventaris')
                ->onDelete('cascade');

            // PERBAIKAN: Menambahkan field quantity
            $table->integer('quantity')->default(1); 

            // Tanggal peminjaman
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->dateTime('tanggal_pengembalian')->nullable();

            $table->text('keterangan')->nullable();

            $table->enum('status', [
                'pending',
                'disetujui',
                'ditolak',
                'selesai'
            ])->default('pending');

            // Persetujuan barang
            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_inventaris');
    }
};