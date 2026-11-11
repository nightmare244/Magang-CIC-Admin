<?php

namespace App\Support;

/**
 * Helper klasifikasi akun CoA Ciwangun Parkland.
 *
 * Semua metode bersifat static dan membaca config/akuntansi.php.
 * Pakai di controller, seeder, atau Blade/Inertia — tidak ada state.
 *
 * Nomor kategori yang valid: 1,2,3,4,5,6,7,8,10,12,13,14,15,16,17
 * (tidak ada nomor 9, 11)
 */
class AkunKategori
{
    /**
     * Ambil detail lengkap satu nomor kategori dari config.
     *
     * @param  int  $nomor
     * @return array<string, mixed>
     */
    public static function detail(int $nomor): array
    {
        return config('akuntansi.kategori.' . $nomor, []);
    }

    /**
     * Nilai enum kolom 'kategori' di tabel akuns.
     * Default 'aset' jika nomor tidak dikenal.
     */
    public static function getKategoriEnum(int $nomor): string
    {
        return self::detail($nomor)['enum'] ?? 'aset';
    }

    /**
     * Saldo normal akun: 'debit' atau 'kredit'.
     */
    public static function getSaldoNormal(int $nomor): string
    {
        return self::detail($nomor)['saldo_normal'] ?? 'debit';
    }

    /**
     * Kelompok laporan: 'neraca' atau 'laba_rugi'.
     */
    public static function getLaporan(int $nomor): string
    {
        return self::detail($nomor)['laporan'] ?? 'neraca';
    }

    /**
     * Posisi di laporan:
     * 'aset' | 'kontra_aset' | 'kewajiban' | 'ekuitas'
     * | 'pendapatan' | 'hpp' | 'beban'
     */
    public static function getSisi(int $nomor): string
    {
        return self::detail($nomor)['sisi'] ?? 'aset';
    }

    /**
     * Jenis arus kas: 'operasional' | 'investasi' | 'pendanaan'.
     */
    public static function getArusKasType(int $nomor): string
    {
        return self::detail($nomor)['arus_kas'] ?? 'operasional';
    }

    /**
     * Apakah akun ini termasuk kelompok Kas & Bank (nomor_kategori 3)?
     */
    public static function isKas(int $nomor): bool
    {
        return !empty(self::detail($nomor)['is_kas']);
    }

    /**
     * Semua nomor kategori yang valid.
     *
     * @return int[]
     */
    public static function allNomor(): array
    {
        return array_keys(config('akuntansi.kategori', []));
    }

    /**
     * Hitung saldo efektif akun dari mutasi Pemasukan/Pengeluaran.
     *
     * Pemasukan  → Debit  Kas, Kredit akun lawan
     * Pengeluaran→ Kredit Kas, Debit  akun lawan
     *
     * Untuk akun lawan:
     * - Jika saldo normal Debit  : saldo += pengeluaran − pemasukan
     * - Jika saldo normal Kredit : saldo += pemasukan  − pengeluaran
     *
     * @param  int    $nomorKategori   Nomor kategori akun (bukan akun kas)
     * @param  float  $totalPemasukan  Total nominal pemasukan lewat akun ini
     * @param  float  $totalPengeluaran Total nominal pengeluaran lewat akun ini
     * @param  float  $saldoAwal
     * @return float
     */
    public static function hitungSaldo(
        int $nomorKategori,
        float $totalPemasukan,
        float $totalPengeluaran,
        float $saldoAwal = 0.0
    ): float {
        $saldoNormal = self::getSaldoNormal($nomorKategori);

        if ($saldoNormal === 'debit') {
            // Saldo naik saat di-debit (pengeluaran), turun saat di-kredit (pemasukan)
            return $saldoAwal + $totalPengeluaran - $totalPemasukan;
        } else {
            // Saldo naik saat di-kredit (pemasukan), turun saat di-debit (pengeluaran)
            return $saldoAwal + $totalPemasukan - $totalPengeluaran;
        }
    }

    /**
     * Hitung saldo akun Kas & Bank.
     *
     * Kas bertambah saat ada Pemasukan (debit kas),
     * Kas berkurang saat ada Pengeluaran (kredit kas).
     *
     * @param  float  $totalPemasukan
     * @param  float  $totalPengeluaran
     * @param  float  $saldoAwal
     * @return float
     */
    public static function hitungSaldoKas(
        float $totalPemasukan,
        float $totalPengeluaran,
        float $saldoAwal = 0.0
    ): float {
        return $saldoAwal + $totalPemasukan - $totalPengeluaran;
    }
}

