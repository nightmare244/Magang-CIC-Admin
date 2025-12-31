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
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke user yang mengajukan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('tipe_izin', ['sakit', 'izin', 'cuti']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            
            $table->text('keterangan');
            $table->string('file_pendukung')->nullable()->comment('Path ke surat dokter, dll');
            
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            
            // Relasi ke admin yang menyetujui/menolak (opsional, tapi bagus)
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('alasan_penolakan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};