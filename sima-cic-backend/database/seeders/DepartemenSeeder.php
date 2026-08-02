<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds idempotently.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $departemens = [
            [
               'nama_departemen' => 'Tenaga Harian Lepas',
               'deskripsi'       => 'Karyawan dengan status tenaga harian lepas.',
            ],
            [
               'nama_departemen' => 'Karyawan',
               'deskripsi'       => 'Karyawan tetap maupun kontrak perusahaan.',
            ],
        ];

        foreach ($departemens as $d) {
            DB::table('departemens')->updateOrInsert(
                ['nama_departemen' => $d['nama_departemen']],
                [
                    'deskripsi'  => $d['deskripsi'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}