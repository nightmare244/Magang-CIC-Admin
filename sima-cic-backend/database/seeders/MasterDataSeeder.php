<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan Foreign Key Checks & Hapus data lama yang relasi ke users
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('absensis')->truncate();
        DB::table('izins')->truncate();
        DB::table('peminjaman_inventaris')->truncate();
        DB::table('log_aktivitas')->truncate();
        DB::table('personal_access_tokens')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pastikan departemen 6 (Karyawan) dan 7 (THL) serta 1 (IT) tersedia
        $itDeptId = DB::table('departemens')->where('id', 1)->value('id') ?? 1;
        $karyawanDeptId = DB::table('departemens')->where('id', 6)->value('id') ?? 6;
        $thlDeptId = DB::table('departemens')->where('id', 7)->value('id') ?? 7;

        $now = Carbon::now();

        // Helper function untuk format password YYYYMMDD dari "DD-MM-YYYY"
        $parseDate = function($dateStr) {
            // Contoh format input: "05-01-1958"
            $parts = explode('-', trim($dateStr));
            if (count($parts) === 3) {
                $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                $year = $parts[2];
                return [
                    'db_date' => "$year-$month-$day",
                    'password' => "$year$month$day"
                ];
            }
            return [
                'db_date' => '2000-01-01',
                'password' => '20000101'
            ];
        };

        // ==========================================
        // 2. DATA KARYAWAN (Sheet 2) - 6 Orang
        // ==========================================
        $karyawanList = [
            [
                'name' => 'Darmiyati',
                'tempat' => 'Bandung',
                'tgl' => '04-05-1984',
                'email' => 'darmi.wisatacic@gmail.com',
                'hp' => '081322513228',
                'is_admin' => false
            ],
            [
                'name' => 'Heru Aziz Santoso',
                'tempat' => 'Batu Patah',
                'tgl' => '16-05-2004',
                'email' => 'heruazizsantoso@gmail.com',
                'hp' => '082121192454',
                'is_admin' => false
            ],
            [
                'name' => 'Ning Sukma Suharti',
                'tempat' => 'Bandung',
                'tgl' => '23-11-1994',
                'email' => 'sukma.sanggara@gmail.com',
                'hp' => '081321339854',
                'is_admin' => false
            ],
            [
                'name' => 'Muhammad Fahmi Novianto',
                'tempat' => 'Bogor',
                'tgl' => '24-11-2001',
                'email' => 'mfahminovianto@gmail.com',
                'hp' => '081511247547',
                'is_admin' => true // ADMIN UTAMA
            ],
            [
                'name' => 'Rahmati Aini',
                'tempat' => 'Jakarta',
                'tgl' => '11-03-1993',
                'email' => 'rainidea21@gmail.com',
                'hp' => '082217249898',
                'is_admin' => false
            ],
            [
                'name' => 'Bayu Mayorda',
                'tempat' => 'Ciamis',
                'tgl' => '15-07-2003',
                'email' => 'bayumayorda0@gmail.com',
                'hp' => '082127535598',
                'is_admin' => false
            ],
        ];

        $nipCounter = 20261001;

        foreach ($karyawanList as $k) {
            $dateData = $parseDate($k['tgl']);
            $nip = (string)$nipCounter++;

            DB::table('users')->insert([
                'name'          => $k['name'],
                'email'         => strtolower(trim($k['email'])),
                'nip'           => $nip,
                'password'      => Hash::make($dateData['password']),
                'tempat_lahir'  => $k['tempat'],
                'tanggal_lahir' => $dateData['db_date'],
                'nomor_hp'      => $k['hp'],
                'departemen_id' => $k['is_admin'] ? $itDeptId : $karyawanDeptId,
                'role'          => $k['is_admin'] ? 'admin' : 'karyawan',
                'kategori'      => 'karyawan',
                'status_kerja'  => 'Aktif',
                'can_absen_thl' => $k['is_admin'] ? true : false,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        // ==========================================
        // 3. DATA THL (Sheet 1) - 18 Orang
        // ==========================================
        $thlList = [
            ['name' => 'Ade Solihin', 'hp' => '081119699990', 'ttl' => 'Bandung, 05-01-1958', 'email' => 'ck.nusantarapt@gmail.com'],
            ['name' => 'SUPOMO', 'hp' => '081119699990', 'ttl' => 'Solo, 18-09-1953', 'email' => 'supomo.ck.nusantara@gmail.com'], // fix duplicate email
            ['name' => 'TITI U', 'hp' => '083113900991', 'ttl' => 'Bandung, 05-04-1974', 'email' => 'saprudintiti6@gmail.com'],
            ['name' => 'SUMIYATI', 'hp' => '083830936061', 'ttl' => 'Bandung, 01-06-1981', 'email' => 'isums826@gmail.com'],
            ['name' => 'DEWI MULYASARI', 'hp' => '089669100937', 'ttl' => 'Bandung, 11-09-1998', 'email' => 'dewimulyasarimulyasaridewi@gmail.com'],
            ['name' => 'HERI SETIAWAN', 'hp' => '0895417660865', 'ttl' => 'Bandung, 10-11-1981', 'email' => 'ismoputri6686@gmail.com'],
            ['name' => 'WAWAN KURNIADI', 'hp' => '081573260757', 'ttl' => 'Bandung, 31-12-1979', 'email' => 'wawankurniadi263@gmail.com'],
            ['name' => 'AHMAD ZAENAL AZIZI', 'hp' => '081770902289', 'ttl' => 'Bandung, 25-07-1997', 'email' => 'zaenalazizi1234@gmail.com'],
            ['name' => 'TARYANA TATA', 'hp' => '085524714087', 'ttl' => 'Bandung, 20-10-1987', 'email' => 'dante.wahyudi821@gmail.com'],
            ['name' => 'DEDE MULYANA', 'hp' => '085797619453', 'ttl' => 'Bandung, 16-11-1991', 'email' => 'demulyana1611@gmail.com'],
            ['name' => 'DIKI ARISANDI', 'hp' => '085891796708', 'ttl' => 'Bandung, 24-02-1997', 'email' => 'arisandidiki@gmail.com'],
            ['name' => 'FIRMAN', 'hp' => '082225318887', 'ttl' => 'Bandung, 23-12-1995', 'email' => '159180734@gmail.com'],
            ['name' => 'BAYU ANGGARA PRATAMA', 'hp' => '085143358319', 'ttl' => 'Gasing, 16-09-2003', 'email' => 'bayuanggarapratama9@gmail.com'],
            ['name' => 'AGUNG SAEFULLOH', 'hp' => '087814179620', 'ttl' => 'Bandung, 07-03-1999', 'email' => 'saepulohagung961@gmail.com'],
            ['name' => 'HERYANA', 'hp' => '081223540369', 'ttl' => 'Tasikmalaya, 09-11-1992', 'email' => 'yheryana588@gmail.com'],
            ['name' => 'GUSTIAR ADITYA PERMANA P', 'hp' => '0895402902747', 'ttl' => 'Bandung, 16-08-2005', 'email' => 'gsstradt1@gmail.com'],
            ['name' => 'DEDI ROSENDA', 'hp' => '087742764095', 'ttl' => 'Bandung, 08-05-1963', 'email' => 'dedirosenda@gmail.com'],
            ['name' => 'SUCI HERAWATI', 'hp' => '083895173587', 'ttl' => 'KAB. Bandung, 19-11-1998', 'email' => 'beruddil@gmail.com'],
        ];

        $nipThlCounter = 20262001;

        foreach ($thlList as $t) {
            // Split tempat dan tanggal lahir
            $ttlParts = explode(',', $t['ttl']);
            $tempat = trim($ttlParts[0] ?? 'Bandung');
            $tglStr = trim($ttlParts[1] ?? '01-01-2000');
            // Hapus spasi di tanggal jika ada misal "19 - 11 - 1998" -> "19-11-1998"
            $tglStr = str_replace(' ', '', $tglStr);

            $dateData = $parseDate($tglStr);
            $nip = (string)$nipThlCounter++;

            DB::table('users')->insert([
                'name'          => ucwords(strtolower($t['name'])),
                'email'         => strtolower(trim($t['email'])),
                'nip'           => $nip,
                'password'      => Hash::make($dateData['password']),
                'tempat_lahir'  => $tempat,
                'tanggal_lahir' => $dateData['db_date'],
                'nomor_hp'      => $t['hp'],
                'departemen_id' => $thlDeptId,
                'role'          => 'karyawan',
                'kategori'      => 'thl',
                'status_kerja'  => 'Aktif',
                'can_absen_thl' => false,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        $this->command->info("✅ Database berhasil diperbarui!");
        $this->command->info("   - Total Karyawan: 6");
        $this->command->info("   - Total THL: 18");
        $this->command->info("   - Admin: Muhammad Fahmi Novianto (NIP: 20261004 | Password: 20011124)");
    }
}
