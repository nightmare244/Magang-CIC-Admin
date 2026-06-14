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
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();          // Simpan nama user (agar tetap terlihat walau user dihapus)
            $table->string('role')->nullable();                // admin / karyawan
            $table->string('aksi');                            // create, update, delete, login, logout, approve, reject, dll
            $table->string('modul');                           // karyawan, absensi, izin, inventaris, peminjaman, pengumuman, pemasukan, pengeluaran, auth, dll
            $table->string('judul');                           // Judul singkat, misal "Menambahkan Pemasukan"
            $table->text('detail')->nullable();                // Detail lengkap, misal "Pemasukan Tiket Masuk - Rp 500.000"
            $table->string('target_id')->nullable();           // ID record yg terdampak (misal id pemasukan)
            $table->string('target_type')->nullable();         // Tipe model (misal App\Models\Pemasukan)
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Index agar query cepat
            $table->index('user_id');
            $table->index('modul');
            $table->index('aksi');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
