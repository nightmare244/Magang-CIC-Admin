<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan tabel departemens sudah ada
        DB::table('departemens')->insert([
            [
                'nama_departemen' => 'IT & Sistem Informasi',
                'deskripsi' => 'Pengembangan dan pemeliharaan sistem informasi perusahaan.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_departemen' => 'Pemasaran & Penjualan',
                'deskripsi' => 'Strategi pemasaran, branding, dan penjualan produk.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_departemen' => 'SDM & Administrasi',
                'deskripsi' => 'Pengelolaan sumber daya manusia dan administrasi umum.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_departemen' => 'Keuangan & Akuntansi',
                'deskripsi' => 'Pengelolaan anggaran, pembukuan, dan laporan keuangan.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}