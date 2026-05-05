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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke user
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            
            $table->enum('status_masuk', ['tepat_waktu', 'terlambat'])->nullable();
            // Anda bisa tambahkan status_pulang (misal: 'tepat_waktu', 'pulang_cepat') jika perlu
            
            $table->string('lokasi_masuk')->nullable()->comment('Koordinat GPS saat masuk');
            $table->string('lokasi_pulang')->nullable()->comment('Koordinat GPS saat pulang');
            
            $table->timestamps();

            // Unique constraint agar 1 user hanya bisa absen 1x per tanggal
            $table->unique(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};