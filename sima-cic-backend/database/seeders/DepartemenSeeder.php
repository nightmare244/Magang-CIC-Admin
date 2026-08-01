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
           'nama_departemen' => 'Tenaga Harian Lepas',
           'deskripsi' => 'Karyawan dengan status tenaga harian lepas.',
           'created_at' => Carbon::now(),
           'updated_at' => Carbon::now(),
        ],
        [
           'nama_departemen' => 'Karyawan',
           'deskripsi' => 'Karyawan tetap maupun kontrak perusahaan.',
           'created_at' => Carbon::now(),
           'updated_at' => Carbon::now(),
        ],
        ]);
    }
}