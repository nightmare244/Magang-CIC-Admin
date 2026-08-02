<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ThlSeeder extends Seeder
{
    /**
     * Seed data THL uji coba:
     * - User: Ade Solihin (kategori=thl)
     * - Absensi: hari ini, diinput oleh Admin Utama (mandor)
     */
    public function run(): void
    {
        // ── 1. Data THL: Ade Solihin ──────────────────────────────────────────
        // Tanggal lahir: 5 Januari 1958 → YYYYMMDD = 19580105 → password default
        $tanggalLahir  = '1958-01-05';
        $passwordDefault = Hash::make('19580105'); // format YYYYMMDD

        // Hapus jika sudah ada (idempoten, aman dijalankan ulang)
        DB::table('users')->where('email', 'ck.nusantarapt@gmail.com')->delete();

        $nipThl = '2026' . rand(10000, 99999); // NIP auto-generate format tahun + 5 digit

        $thlId = DB::table('users')->insertGetId([
            'name'          => 'Ade Solihin',
            'email'         => 'ck.nusantarapt@gmail.com',
            'nip'           => $nipThl,
            'password'      => $passwordDefault,
            'tempat_lahir'  => 'Bandung',
            'tanggal_lahir' => $tanggalLahir,
            'nomor_hp'      => '081119699990',
            'departemen_id' => 7,         // Departemen "THL" (ID 7)
            'role'          => 'karyawan', // Bisa login sebagai karyawan
            'kategori'      => 'thl',      // Tapi kategorinya THL
            'status_kerja'  => 'Aktif',
            'can_absen_thl' => false,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ]);

        $this->command->info("✅ THL 'Ade Solihin' berhasil dibuat. NIP: $nipThl | ID: $thlId");

        // ── 2. Ambil ID Admin Utama sebagai "mandor" (input_by) ───────────────
        $adminId = DB::table('users')->where('role', 'admin')->value('id');

        if (!$adminId) {
            $this->command->warn("⚠️  Admin tidak ditemukan. Absensi seed dilewati.");
            return;
        }

        // ── 3. Data absensi uji coba untuk Ade Solihin ────────────────────────
        $today = Carbon::today()->toDateString();

        // Hapus absensi hari ini jika sudah ada (idempoten)
        DB::table('absensis')
            ->where('user_id', $thlId)
            ->where('tanggal', $today)
            ->delete();

        DB::table('absensis')->insert([
            'user_id'      => $thlId,
            'input_by'     => $adminId,  // Diinput oleh Admin (mandor)
            'tanggal'      => $today,
            'jam_masuk'    => '08:00:00',
            'jam_pulang'   => null,
            'status_hari'  => 'HADIR',
            'status_masuk' => 'tepat_waktu',
            'metode'       => 'diinput_mandor',
            'status_absen' => 'hadir',
            'keterangan'   => 'Input oleh Admin (seed uji coba THL)',
            'lokasi_masuk' => '-6.680611,107.517056',
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ]);

        $this->command->info("✅ Absensi hari ini ($today) untuk Ade Solihin berhasil diseed.");
        $this->command->line("   → metode: diinput_mandor | input_by: Admin (ID: $adminId)");
        $this->command->line("   → Login THL: NIP=$nipThl | Password=19580105");
    }
}
