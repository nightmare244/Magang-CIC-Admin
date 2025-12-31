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
        Schema::create('pengumuman_reads', function (Blueprint $table) {
            $table->id();
            
            // Karyawan yang membaca
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Pengumuman yang dibaca
            $table->foreignId('pengumuman_id')->constrained('pengumumans')->onDelete('cascade');
            
            $table->timestamp('read_at')->useCurrent();
            
            $table->timestamps();

            // Pastikan 1 user hanya bisa baca 1x
            $table->unique(['user_id', 'pengumuman_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_reads');
    }
};