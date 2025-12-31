<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'kode_barang' => 'INV-00001',
                'nama_barang' => 'Laptop ThinkPad X1 Carbon',
                'deskripsi' => 'Laptop kantor untuk kebutuhan developer. Kondisi 95%.',
                'quantity' => 5,
                'harga_satuan' => 15000000.00,
                'nilai_barang' => 75000000.00,
                'status_ketersediaan' => 'tersedia',
                'qr_code_string' => 'QR-INV-X1-A1',
            ],
            [
                'id' => 2,
                'kode_barang' => 'INV-00002',
                'nama_barang' => 'Proyektor Mini Epson',
                'deskripsi' => 'Digunakan untuk meeting dan presentasi.',
                'quantity' => 1,
                'harga_satuan' => 4500000.00,
                'nilai_barang' => 4500000.00,
                'status_ketersediaan' => 'dipinjam', // Status dipinjam untuk pengujian
                'qr_code_string' => 'QR-INV-PR-B2',
            ],
            [
                'id' => 3,
                'kode_barang' => 'INV-00003',
                'nama_barang' => 'Kamera Canon EOS R10',
                'deskripsi' => 'Untuk kebutuhan dokumentasi marketing.',
                'quantity' => 2,
                'harga_satuan' => 12000000.00,
                'nilai_barang' => 24000000.00,
                'status_ketersediaan' => 'tersedia',
                'qr_code_string' => 'QR-INV-CAM-C3',
            ],
        ];

        foreach ($data as $item) {
            DB::table('inventaris')->insert(array_merge($item, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
}