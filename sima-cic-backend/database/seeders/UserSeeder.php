<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari ID Departemen IT secara dinamis, jika tidak ada default ke 1
        $itDepartemenId = DB::table('departemens')->where('nama_departemen', 'LIKE', '%IT%')->value('id') ?? 1;
        
        // Hapus data admin lama jika ada (opsional, agar tidak duplikat saat seeder dijalankan ulang)
        DB::table('users')->where('email', 'admin@cic.com')->delete();

        // 1. INPUT ADMIN UTAMA
        DB::table('users')->insert([
            'name'          => 'Admin Utama',
            'email'         => 'admin@cic.com',
            'nip'           => '202500001',
            'password'      => Hash::make('password'), // Password untuk login: password
            'role'          => 'admin',
            'status_kerja'  => 'Permanent', // Menggunakan kolom baru status_kerja
            'departemen_id' => $itDepartemenId,
            'tempat_lahir'  => 'Bandung',
            'tanggal_lahir' => Carbon::create(1990, 1, 1),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ]);
    }
}