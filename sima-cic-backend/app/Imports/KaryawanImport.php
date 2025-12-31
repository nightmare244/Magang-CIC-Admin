<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
// Import helper tanggal Excel
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KaryawanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Logika Konversi Tanggal Excel ke PHP [PENTING]
        $tanggalLahir = $row['tanggal_lahir'];
        
        // Cek jika formatnya adalah angka serial Excel (seperti 35935)
        if (is_numeric($tanggalLahir)) {
            $dateObject = Date::excelToDateTimeObject($tanggalLahir);
            $tglLahirRaw = $dateObject->format('Y-m-d');
        } else {
            // Jika sudah string (YYYY-MM-DD), langsung ambil
            $tglLahirRaw = $tanggalLahir;
        }

        // 2. Logika Password Otomatis dari Tanggal Lahir (YYYYMMDD)
        $passwordDefault = str_replace('-', '', (string)$tglLahirRaw);

        // 3. Logika NIP Otomatis
        $nipOtomatis = date('Y') . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        return new User([
            'name'            => $row['nama'],
            'email'           => $row['email'],
            'nip'             => $nipOtomatis,
            'password'        => Hash::make($passwordDefault),
            'tempat_lahir'    => $row['tempat_lahir'],
            'tanggal_lahir'   => $tglLahirRaw, // Sekarang formatnya sudah YYYY-MM-DD
            'jenis_kelamin'   => (Str::upper($row['jenis_kelamin']) == 'L') ? 'L' : 'P',
            'role'            => 'karyawan',
            'is_active'       => true,
            'departemen_id'   => null,
        ]);
    }
}