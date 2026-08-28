<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Akun;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akuns = [
            // 1. ASET (1-XXXXX)
            [
                'kode_akun'    => '1-10001',
                'nama_akun'    => 'Kas Utama (Tunai)',
                'kategori'     => 'aset',
                'saldo_normal' => 'debit',
                'saldo_awal'   => 10000000,
                'is_active'    => true,
                'keterangan'   => 'Kas fisik di brankas / kantor',
            ],
            [
                'kode_akun'    => '1-10002',
                'nama_akun'    => 'Rekening Bank Operasional',
                'kategori'     => 'aset',
                'saldo_normal' => 'debit',
                'saldo_awal'   => 25000000,
                'is_active'    => true,
                'keterangan'   => 'Rekening bank untuk transaksi transfer',
            ],

            // 2. KEWAJIBAN (2-XXXXX)
            [
                'kode_akun'    => '2-10001',
                'nama_akun'    => 'Hutang Usaha & Operasional',
                'kategori'     => 'kewajiban',
                'saldo_normal' => 'kredit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Kewajiban pembayaran jangka pendek',
            ],

            // 3. EKUITAS / MODAL (3-XXXXX)
            [
                'kode_akun'    => '3-10001',
                'nama_akun'    => 'Modal Awal Yayasan / Perusahaan',
                'kategori'     => 'ekuitas',
                'saldo_normal' => 'kredit',
                'saldo_awal'   => 35000000,
                'is_active'    => true,
                'keterangan'   => 'Modal disetor dan saldo awal',
            ],

            // 4. PENDAPATAN (4-XXXXX)
            [
                'kode_akun'    => '4-10001',
                'nama_akun'    => 'Pendapatan Tiket Masuk',
                'kategori'     => 'pendapatan',
                'saldo_normal' => 'kredit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Penjualan tiket pengunjung reguler dan rombongan',
            ],
            [
                'kode_akun'    => '4-10002',
                'nama_akun'    => 'Pendapatan Donasi',
                'kategori'     => 'pendapatan',
                'saldo_normal' => 'kredit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Penerimaan donasi individu, yayasan, atau organisasi',
            ],
            [
                'kode_akun'    => '4-10003',
                'nama_akun'    => 'Pendapatan Sponsorship & Hibah',
                'kategori'     => 'pendapatan',
                'saldo_normal' => 'kredit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Dana sponsor kegiatan, program edukasi, dan konservasi',
            ],
            [
                'kode_akun'    => '4-10004',
                'nama_akun'    => 'Pendapatan Lain-lain (Parkir & Souvenir)',
                'kategori'     => 'pendapatan',
                'saldo_normal' => 'kredit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Pendapatan di luar tiket (parkir, souvenir, sewa venue)',
            ],

            // 5. BEBAN / PENGELUARAN (5-XXXXX)
            [
                'kode_akun'    => '5-10001',
                'nama_akun'    => 'Beban Gaji & Honor Karyawan/THL',
                'kategori'     => 'beban',
                'saldo_normal' => 'debit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Pembayaran gaji pokok, lembur, dan tunjangan pegawai',
            ],
            [
                'kode_akun'    => '5-10002',
                'nama_akun'    => 'Beban Operasional & Pakan/Bahan',
                'kategori'     => 'beban',
                'saldo_normal' => 'debit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Biaya operasional harian, pakan, obat-obatan, dan ATK',
            ],
            [
                'kode_akun'    => '5-10003',
                'nama_akun'    => 'Beban Pemeliharaan & Perbaikan',
                'kategori'     => 'beban',
                'saldo_normal' => 'debit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Biaya perawatan kandang, renovasi fasilitas, dan taman',
            ],
            [
                'kode_akun'    => '5-10004',
                'nama_akun'    => 'Beban Listrik, Air & Utilitas',
                'kategori'     => 'beban',
                'saldo_normal' => 'debit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Tagihan listrik, air PDAM, internet, dan kebersihan',
            ],
            [
                'kode_akun'    => '5-10005',
                'nama_akun'    => 'Beban Umum & Lain-lain',
                'kategori'     => 'beban',
                'saldo_normal' => 'debit',
                'saldo_awal'   => 0,
                'is_active'    => true,
                'keterangan'   => 'Biaya administrasi, tak terduga, dan konsumsi rapat',
            ],
        ];

        foreach ($akuns as $item) {
            Akun::updateOrCreate(
                ['kode_akun' => $item['kode_akun']],
                $item
            );
        }

        // ==========================================
        // AUTO-MAP EXISTING TRANSACTIONS KE COA
        // ==========================================
        $akunTiket   = Akun::where('kode_akun', '4-10001')->first();
        $akunDonasi  = Akun::where('kode_akun', '4-10002')->first();
        $akunSponsor = Akun::where('kode_akun', '4-10003')->first();
        $akunLainnya = Akun::where('kode_akun', '4-10004')->first();

        Pemasukan::whereNull('akun_id')->orWhere('akun_id', 0)->chunk(100, function ($records) use ($akunTiket, $akunDonasi, $akunSponsor, $akunLainnya) {
            foreach ($records as $p) {
                $targetAkunId = match ($p->tipe) {
                    'tiket_masuk' => $akunTiket?->id,
                    'donasi'      => $akunDonasi?->id,
                    'sponsor'     => $akunSponsor?->id,
                    default       => $akunLainnya?->id ?? $akunTiket?->id,
                };
                $p->update(['akun_id' => $targetAkunId]);
            }
        });

        $akunGaji        = Akun::where('kode_akun', '5-10001')->first();
        $akunOperasional = Akun::where('kode_akun', '5-10002')->first();
        $akunMaintenance = Akun::where('kode_akun', '5-10003')->first();
        $akunUtility     = Akun::where('kode_akun', '5-10004')->first();
        $akunBebanLain   = Akun::where('kode_akun', '5-10005')->first();

        Pengeluaran::whereNull('akun_id')->orWhere('akun_id', 0)->chunk(100, function ($records) use ($akunGaji, $akunOperasional, $akunMaintenance, $akunUtility, $akunBebanLain) {
            foreach ($records as $p) {
                $targetAkunId = match ($p->kategori) {
                    'gaji'        => $akunGaji?->id,
                    'operasional' => $akunOperasional?->id,
                    'maintenance' => $akunMaintenance?->id,
                    'utility'     => $akunUtility?->id,
                    default       => $akunBebanLain?->id ?? $akunOperasional?->id,
                };
                $p->update(['akun_id' => $targetAkunId]);
            }
        });

        $this->command?->info('✅ Daftar Akun (CoA) berhasil di-seed dan transaksi lama telah dipetakan.');
    }
}
