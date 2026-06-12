<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class KeuanganSeeder extends Seeder
{
    /**
     * Seed data pemasukan dan pengeluaran untuk 6 bulan terakhir.
     */
    public function run(): void
    {
        // Clear existing data
        Pemasukan::truncate();
        Pengeluaran::truncate();

        $now = Carbon::today();

        // === DATA PEMASUKAN (6 bulan terakhir) ===
        $pemasukanData = [
            // 5 bulan lalu
            ['nama_pemasukan' => 'Tiket Masuk Reguler', 'tipe' => 'tiket_masuk', 'jumlah' => 150, 'nominal' => 3750000, 'tanggal_pemasukan' => $now->copy()->subMonths(5)->startOfMonth()->addDays(4)->toDateString(), 'keterangan' => 'Penjualan tiket masuk minggu pertama'],
            ['nama_pemasukan' => 'Donasi Yayasan ABC', 'tipe' => 'donasi', 'jumlah' => 1, 'nominal' => 500000, 'tanggal_pemasukan' => $now->copy()->subMonths(5)->startOfMonth()->addDays(10)->toDateString(), 'keterangan' => 'Donasi dari yayasan ABC'],

            // 4 bulan lalu
            ['nama_pemasukan' => 'Tiket Masuk Reguler', 'tipe' => 'tiket_masuk', 'jumlah' => 200, 'nominal' => 5000000, 'tanggal_pemasukan' => $now->copy()->subMonths(4)->startOfMonth()->addDays(3)->toDateString(), 'keterangan' => 'Penjualan tiket masuk'],
            ['nama_pemasukan' => 'Sponsor Event Edukasi', 'tipe' => 'sponsor', 'jumlah' => 1, 'nominal' => 2000000, 'tanggal_pemasukan' => $now->copy()->subMonths(4)->startOfMonth()->addDays(15)->toDateString(), 'keterangan' => 'Sponsor event edukasi alam'],

            // 3 bulan lalu
            ['nama_pemasukan' => 'Tiket Masuk Reguler', 'tipe' => 'tiket_masuk', 'jumlah' => 250, 'nominal' => 6250000, 'tanggal_pemasukan' => $now->copy()->subMonths(3)->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Penjualan tiket masuk'],
            ['nama_pemasukan' => 'Donasi Individu', 'tipe' => 'donasi', 'jumlah' => 3, 'nominal' => 1500000, 'tanggal_pemasukan' => $now->copy()->subMonths(3)->startOfMonth()->addDays(12)->toDateString(), 'keterangan' => 'Donasi dari pengunjung'],
            ['nama_pemasukan' => 'Pendapatan Parkir', 'tipe' => 'lainnya', 'jumlah' => 1, 'nominal' => 800000, 'tanggal_pemasukan' => $now->copy()->subMonths(3)->startOfMonth()->addDays(20)->toDateString(), 'keterangan' => 'Pendapatan parkir bulan ini'],

            // 2 bulan lalu
            ['nama_pemasukan' => 'Tiket Masuk Reguler', 'tipe' => 'tiket_masuk', 'jumlah' => 320, 'nominal' => 8000000, 'tanggal_pemasukan' => $now->copy()->subMonths(2)->startOfMonth()->addDays(2)->toDateString(), 'keterangan' => 'Penjualan tiket masuk'],
            ['nama_pemasukan' => 'Sponsor Konservasi', 'tipe' => 'sponsor', 'jumlah' => 1, 'nominal' => 3000000, 'tanggal_pemasukan' => $now->copy()->subMonths(2)->startOfMonth()->addDays(8)->toDateString(), 'keterangan' => 'Sponsor program konservasi'],
            ['nama_pemasukan' => 'Sewa Venue', 'tipe' => 'lainnya', 'jumlah' => 2, 'nominal' => 1500000, 'tanggal_pemasukan' => $now->copy()->subMonths(2)->startOfMonth()->addDays(18)->toDateString(), 'keterangan' => 'Sewa venue acara'],

            // 1 bulan lalu
            ['nama_pemasukan' => 'Tiket Masuk Reguler', 'tipe' => 'tiket_masuk', 'jumlah' => 400, 'nominal' => 10000000, 'tanggal_pemasukan' => $now->copy()->subMonths(1)->startOfMonth()->addDays(1)->toDateString(), 'keterangan' => 'Penjualan tiket masuk'],
            ['nama_pemasukan' => 'Donasi Corporate', 'tipe' => 'donasi', 'jumlah' => 1, 'nominal' => 2500000, 'tanggal_pemasukan' => $now->copy()->subMonths(1)->startOfMonth()->addDays(14)->toDateString(), 'keterangan' => 'Donasi dari perusahaan XYZ'],
            ['nama_pemasukan' => 'Pendapatan Souvenir', 'tipe' => 'lainnya', 'jumlah' => 1, 'nominal' => 1200000, 'tanggal_pemasukan' => $now->copy()->subMonths(1)->startOfMonth()->addDays(22)->toDateString(), 'keterangan' => 'Penjualan souvenir'],

            // Bulan ini
            ['nama_pemasukan' => 'Tiket Masuk Reguler', 'tipe' => 'tiket_masuk', 'jumlah' => 180, 'nominal' => 4500000, 'tanggal_pemasukan' => $now->copy()->startOfMonth()->addDays(1)->toDateString(), 'keterangan' => 'Penjualan tiket masuk minggu pertama'],
            ['nama_pemasukan' => 'Sponsor Event', 'tipe' => 'sponsor', 'jumlah' => 1, 'nominal' => 5000000, 'tanggal_pemasukan' => $now->copy()->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Sponsor event konservasi besar'],
            ['nama_pemasukan' => 'Donasi Online', 'tipe' => 'donasi', 'jumlah' => 10, 'nominal' => 2000000, 'tanggal_pemasukan' => $now->copy()->startOfMonth()->addDays(8)->toDateString(), 'keterangan' => 'Donasi melalui platform online'],
        ];

        foreach ($pemasukanData as $data) {
            Pemasukan::create($data);
        }

        // === DATA PENGELUARAN (6 bulan terakhir) ===
        $pengeluaranData = [
            // 5 bulan lalu
            ['nama_pengeluaran' => 'Gaji Karyawan', 'kategori' => 'gaji', 'nominal' => 2000000, 'tanggal_pengeluaran' => $now->copy()->subMonths(5)->startOfMonth()->addDays(0)->toDateString(), 'keterangan' => 'Pembayaran gaji bulanan'],
            ['nama_pengeluaran' => 'Listrik & Air', 'kategori' => 'utility', 'nominal' => 500000, 'tanggal_pengeluaran' => $now->copy()->subMonths(5)->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Tagihan listrik dan air'],

            // 4 bulan lalu
            ['nama_pengeluaran' => 'Gaji Karyawan', 'kategori' => 'gaji', 'nominal' => 2000000, 'tanggal_pengeluaran' => $now->copy()->subMonths(4)->startOfMonth()->addDays(0)->toDateString(), 'keterangan' => 'Pembayaran gaji bulanan'],
            ['nama_pengeluaran' => 'Perbaikan Kandang', 'kategori' => 'maintenance', 'nominal' => 1500000, 'tanggal_pengeluaran' => $now->copy()->subMonths(4)->startOfMonth()->addDays(10)->toDateString(), 'keterangan' => 'Perbaikan kandang area A'],
            ['nama_pengeluaran' => 'Listrik & Air', 'kategori' => 'utility', 'nominal' => 550000, 'tanggal_pengeluaran' => $now->copy()->subMonths(4)->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Tagihan listrik dan air'],

            // 3 bulan lalu
            ['nama_pengeluaran' => 'Gaji Karyawan', 'kategori' => 'gaji', 'nominal' => 2000000, 'tanggal_pengeluaran' => $now->copy()->subMonths(3)->startOfMonth()->addDays(0)->toDateString(), 'keterangan' => 'Pembayaran gaji bulanan'],
            ['nama_pengeluaran' => 'Bahan Operasional', 'kategori' => 'operasional', 'nominal' => 800000, 'tanggal_pengeluaran' => $now->copy()->subMonths(3)->startOfMonth()->addDays(7)->toDateString(), 'keterangan' => 'Pembelian pakan hewan'],
            ['nama_pengeluaran' => 'Listrik & Air', 'kategori' => 'utility', 'nominal' => 600000, 'tanggal_pengeluaran' => $now->copy()->subMonths(3)->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Tagihan listrik dan air'],
            ['nama_pengeluaran' => 'Biaya Lainnya', 'kategori' => 'lainnya', 'nominal' => 300000, 'tanggal_pengeluaran' => $now->copy()->subMonths(3)->startOfMonth()->addDays(15)->toDateString(), 'keterangan' => 'Biaya tak terduga'],

            // 2 bulan lalu
            ['nama_pengeluaran' => 'Gaji Karyawan', 'kategori' => 'gaji', 'nominal' => 2200000, 'tanggal_pengeluaran' => $now->copy()->subMonths(2)->startOfMonth()->addDays(0)->toDateString(), 'keterangan' => 'Pembayaran gaji bulanan (ada lembur)'],
            ['nama_pengeluaran' => 'Renovasi Toilet', 'kategori' => 'maintenance', 'nominal' => 2000000, 'tanggal_pengeluaran' => $now->copy()->subMonths(2)->startOfMonth()->addDays(12)->toDateString(), 'keterangan' => 'Renovasi fasilitas toilet pengunjung'],
            ['nama_pengeluaran' => 'Listrik & Air', 'kategori' => 'utility', 'nominal' => 650000, 'tanggal_pengeluaran' => $now->copy()->subMonths(2)->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Tagihan listrik dan air'],
            ['nama_pengeluaran' => 'Operasional Harian', 'kategori' => 'operasional', 'nominal' => 1000000, 'tanggal_pengeluaran' => $now->copy()->subMonths(2)->startOfMonth()->addDays(20)->toDateString(), 'keterangan' => 'Biaya operasional harian'],

            // 1 bulan lalu
            ['nama_pengeluaran' => 'Gaji Karyawan', 'kategori' => 'gaji', 'nominal' => 2200000, 'tanggal_pengeluaran' => $now->copy()->subMonths(1)->startOfMonth()->addDays(0)->toDateString(), 'keterangan' => 'Pembayaran gaji bulanan'],
            ['nama_pengeluaran' => 'Pembelian Obat Hewan', 'kategori' => 'operasional', 'nominal' => 1500000, 'tanggal_pengeluaran' => $now->copy()->subMonths(1)->startOfMonth()->addDays(8)->toDateString(), 'keterangan' => 'Obat-obatan untuk hewan'],
            ['nama_pengeluaran' => 'Listrik & Air', 'kategori' => 'utility', 'nominal' => 700000, 'tanggal_pengeluaran' => $now->copy()->subMonths(1)->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Tagihan listrik dan air'],
            ['nama_pengeluaran' => 'Perawatan Taman', 'kategori' => 'maintenance', 'nominal' => 800000, 'tanggal_pengeluaran' => $now->copy()->subMonths(1)->startOfMonth()->addDays(16)->toDateString(), 'keterangan' => 'Perawatan taman dan landscape'],

            // Bulan ini
            ['nama_pengeluaran' => 'Gaji Karyawan', 'kategori' => 'gaji', 'nominal' => 2200000, 'tanggal_pengeluaran' => $now->copy()->startOfMonth()->addDays(0)->toDateString(), 'keterangan' => 'Pembayaran gaji bulanan'],
            ['nama_pengeluaran' => 'Operasional Harian', 'kategori' => 'operasional', 'nominal' => 1200000, 'tanggal_pengeluaran' => $now->copy()->startOfMonth()->addDays(3)->toDateString(), 'keterangan' => 'Biaya operasional harian'],
            ['nama_pengeluaran' => 'Listrik & Air', 'kategori' => 'utility', 'nominal' => 750000, 'tanggal_pengeluaran' => $now->copy()->startOfMonth()->addDays(5)->toDateString(), 'keterangan' => 'Tagihan listrik dan air'],
        ];

        foreach ($pengeluaranData as $data) {
            Pengeluaran::create($data);
        }

        $this->command->info('✅ Data keuangan berhasil di-seed: ' . count($pemasukanData) . ' pemasukan, ' . count($pengeluaranData) . ' pengeluaran.');
    }
}
