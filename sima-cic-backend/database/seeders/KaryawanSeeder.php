<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KaryawanSeeder extends Seeder
{
    /**
     * Seed 1 data uji karyawan tetap: Darmiyati
     * - kategori = karyawan (bisa self-absen)
     * - role = karyawan (akses dashboard karyawan)
     * - Password default: YYYYMMDD dari tanggal lahir
     */
    public function run(): void
    {
        // Tanggal lahir: 04-05-1984 → YYYYMMDD = 19840504
        $tanggalLahir    = '1984-05-04';
        $passwordDefault = Hash::make('19840504');

        // Hapus jika sudah ada (idempoten)
        DB::table('users')->where('email', 'darmi.wisatacic@gmail.com')->delete();

        $nip = '2026' . rand(10000, 99999);

        $userId = DB::table('users')->insertGetId([
            'name'          => 'Darmiyati',
            'email'         => 'darmi.wisatacic@gmail.com',
            'nip'           => $nip,
            'password'      => $passwordDefault,
            'tempat_lahir'  => 'Bandung',
            'tanggal_lahir' => $tanggalLahir,
            'nomor_hp'      => '081322513228',
            'departemen_id' => 6,           // Departemen "Karyawan" (ID 6)
            'role'          => 'karyawan',  // Akses login karyawan
            'kategori'      => 'karyawan',  // Karyawan tetap → boleh self-absen
            'status_kerja'  => 'Aktif',
            'can_absen_thl' => false,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ]);

        $this->command->info("✅ Karyawan 'Darmiyati' berhasil dibuat.");
        $this->command->line("   → NIP      : $nip");
        $this->command->line("   → ID       : $userId");
        $this->command->line("   → Login    : NIP=$nip | Password=19840504");
        $this->command->line("   → Kategori : karyawan (boleh self-absen)");
    }
}
