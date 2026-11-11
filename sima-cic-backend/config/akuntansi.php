<?php

/**
 * Konfigurasi akuntansi terpusat untuk SIMA CIC.
 *
 * Satu tempat untuk semua aturan klasifikasi akun CoA Ciwangun Parkland.
 * Dipakai oleh: AkunKategori helper, LaporanKeuanganController,
 *               KeuanganController, AkunController, dan CoaCiwangunSeeder.
 *
 * nomor_kategori -> informasi lengkap akun
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Mapping Nomor Kategori -> Detail
    |--------------------------------------------------------------------------
    | nama        : Nama resmi kategori (satu nomor = satu nama, sudah diseragamkan)
    | laporan     : 'neraca' atau 'laba_rugi'
    | sisi        : posisi di laporan keuangan
    | saldo_normal: 'debit' atau 'kredit'
    | arus_kas    : jenis arus kas
    | enum        : nilai kolom enum 'kategori' di tabel akuns
    | is_kas      : true jika akun ini adalah akun Kas & Bank
    */
    'kategori' => [
        1  => [
            'nama'         => 'Akun Piutang',
            'laporan'      => 'neraca',
            'sisi'         => 'aset',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'operasional',
            'enum'         => 'aset',
        ],
        2  => [
            'nama'         => 'Aktiva Lancar Lainnya',
            'laporan'      => 'neraca',
            'sisi'         => 'aset',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'operasional',
            'enum'         => 'aset',
        ],
        3  => [
            'nama'         => 'Kas & Bank',
            'laporan'      => 'neraca',
            'sisi'         => 'aset',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'operasional',
            'enum'         => 'aset',
            'is_kas'       => true,
        ],
        4  => [
            'nama'         => 'Persediaan',
            'laporan'      => 'neraca',
            'sisi'         => 'aset',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'operasional',
            'enum'         => 'aset',
        ],
        5  => [
            'nama'         => 'Aktiva Tetap',
            'laporan'      => 'neraca',
            'sisi'         => 'aset',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'investasi',
            'enum'         => 'aset',
        ],
        6  => [
            'nama'         => 'Aktiva Lainnya',
            'laporan'      => 'neraca',
            'sisi'         => 'aset',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'investasi',
            'enum'         => 'aset',
        ],
        7  => [
            'nama'         => 'Depresiasi & Amortisasi',
            'laporan'      => 'neraca',
            'sisi'         => 'kontra_aset',  // kontra aset: saldo_normal kredit tapi masuk sisi aset
            'saldo_normal' => 'kredit',
            'arus_kas'     => 'investasi',
            'enum'         => 'aset',
        ],
        8  => [
            'nama'         => 'Akun Hutang',
            'laporan'      => 'neraca',
            'sisi'         => 'kewajiban',
            'saldo_normal' => 'kredit',
            'arus_kas'     => 'pendanaan',
            'enum'         => 'kewajiban',
        ],
        10 => [
            'nama'         => 'Kewajiban Lancar Lainnya',
            'laporan'      => 'neraca',
            'sisi'         => 'kewajiban',
            'saldo_normal' => 'kredit',
            'arus_kas'     => 'operasional',
            'enum'         => 'kewajiban',
        ],
        12 => [
            'nama'         => 'Ekuitas',
            'laporan'      => 'neraca',
            'sisi'         => 'ekuitas',
            'saldo_normal' => 'kredit',
            'arus_kas'     => 'pendanaan',
            'enum'         => 'ekuitas',
        ],
        13 => [
            'nama'         => 'Pendapatan',
            'laporan'      => 'laba_rugi',
            'sisi'         => 'pendapatan',
            'saldo_normal' => 'kredit',
            'arus_kas'     => 'operasional',
            'enum'         => 'pendapatan',
        ],
        14 => [
            'nama'         => 'Pendapatan Lainnya',
            'laporan'      => 'laba_rugi',
            'sisi'         => 'pendapatan',
            'saldo_normal' => 'kredit',
            'arus_kas'     => 'operasional',
            'enum'         => 'pendapatan',
        ],
        15 => [
            'nama'         => 'Harga Pokok Penjualan',
            'laporan'      => 'laba_rugi',
            'sisi'         => 'hpp',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'operasional',
            'enum'         => 'beban',
        ],
        16 => [
            'nama'         => 'Beban',
            'laporan'      => 'laba_rugi',
            'sisi'         => 'beban',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'operasional',
            'enum'         => 'beban',
        ],
        17 => [
            'nama'         => 'Beban Lainnya',
            'laporan'      => 'laba_rugi',
            'sisi'         => 'beban',
            'saldo_normal' => 'debit',
            'arus_kas'     => 'operasional',
            'enum'         => 'beban',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Grup Nomor Kategori (shortcut)
    |--------------------------------------------------------------------------
    */
    'kas_nomor_kategori'       => [3],
    'aset_nomor_kategori'      => [1, 2, 3, 4, 5, 6, 7],
    'kewajiban_nomor_kategori' => [8, 10],
    'ekuitas_nomor_kategori'   => [12],
    'pendapatan_nomor_kategori'=> [13, 14],
    'hpp_nomor_kategori'       => [15],
    'beban_nomor_kategori'     => [15, 16, 17],

    /*
    |--------------------------------------------------------------------------
    | Klasifikasi Arus Kas
    |--------------------------------------------------------------------------
    | Berdasarkan nomor_kategori akun LAWAN (bukan akun kas).
    */
    'arus_kas' => [
        'operasional' => [1, 2, 4, 10, 13, 14, 15, 16, 17],
        'investasi'   => [5, 6, 7],
        'pendanaan'   => [8, 12],
    ],

];

