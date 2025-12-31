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
        // Asumsi Departemen IT ID = 1 dan SDM ID = 3
        $itDepartemenId = DB::table('departemens')->where('nama_departemen', 'IT & Sistem Informasi')->value('id') ?? 1;
        $hrDepartemenId = DB::table('departemens')->where('nama_departemen', 'SDM & Administrasi')->value('id') ?? 3;
        
        // 1. ADMIN UTAMA
        DB::table('users')->insert([
            'name' => 'Admin Utama',
            'email' => 'admin@cic.com',
            'nip' => '202500001',
            'password' => Hash::make('password'), // password: password
            'role' => 'admin',
            'is_active' => true,
            'departemen_id' => $itDepartemenId,
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => Carbon::create(1990, 1, 1),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. KARYAWAN DEMO (Data Lengkap)
        DB::table('users')->insert([
            'name' => 'Budi Santoso',
            'email' => 'karyawan@cic.com',
            'nip' => '202500002',
            'password' => Hash::make('password'), // password: password
            'role' => 'karyawan',
            'is_active' => true,
            'departemen_id' => $itDepartemenId,
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => Carbon::create(1995, 5, 15),
            'jenis_kelamin' => 'L',
            'nomor_hp' => '081234567890',
            'alamat' => 'Jl. Kebon Jeruk No. 10, Jakarta Barat',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // 3. KARYAWAN BARU (Data Minimal - untuk diisi sendiri)
        DB::table('users')->insert([
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@cic.com',
            'nip' => '202500003',
            'password' => Hash::make('password'), // password: password
            'role' => 'karyawan',
            'is_active' => true,
            'departemen_id' => $hrDepartemenId,
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => Carbon::create(2000, 10, 20),
            // Data lainnya NULL untuk diselesaikan oleh Karyawan (Self-Service)
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}