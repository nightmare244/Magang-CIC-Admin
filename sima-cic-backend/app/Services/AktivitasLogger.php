<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AktivitasLogger
{
    /**
     * Catat log aktivitas ke database.
     *
     * @param string      $aksi       Jenis aksi: create, update, delete, login, logout, approve, reject, return
     * @param string      $modul      Nama modul: auth, karyawan, absensi, izin, inventaris, peminjaman, pengumuman, pemasukan, pengeluaran, departemen
     * @param string      $judul      Judul singkat, misal "Menambahkan pemasukan baru"
     * @param string|null $detail     Detail lengkap (opsional), misal "Tiket Masuk - Rp 500.000"
     * @param mixed|null  $target     Object/model yang terdampak (opsional)
     */
    public static function log(string $aksi, string $modul, string $judul, ?string $detail = null, $target = null): LogAktivitas
    {
        $user = Auth::user();

        return LogAktivitas::create([
            'user_id'     => $user?->id,
            'user_name'   => $user?->name ?? 'System',
            'role'        => $user?->role ?? 'system',
            'aksi'        => $aksi,
            'modul'       => $modul,
            'judul'       => $judul,
            'detail'      => $detail,
            'target_id'   => $target?->id ?? ($target ? (string) $target : null),
            'target_type' => $target ? get_class($target) : null,
            'ip_address'  => Request::ip(),
        ]);
    }

    /**
     * Shortcut: Log pembuatan resource
     */
    public static function created(string $modul, string $judul, ?string $detail = null, $target = null): LogAktivitas
    {
        return self::log('create', $modul, $judul, $detail, $target);
    }

    /**
     * Shortcut: Log update resource
     */
    public static function updated(string $modul, string $judul, ?string $detail = null, $target = null): LogAktivitas
    {
        return self::log('update', $modul, $judul, $detail, $target);
    }

    /**
     * Shortcut: Log hapus resource
     */
    public static function deleted(string $modul, string $judul, ?string $detail = null, $target = null): LogAktivitas
    {
        return self::log('delete', $modul, $judul, $detail, $target);
    }

    /**
     * Shortcut: Log approve
     */
    public static function approved(string $modul, string $judul, ?string $detail = null, $target = null): LogAktivitas
    {
        return self::log('approve', $modul, $judul, $detail, $target);
    }

    /**
     * Shortcut: Log reject
     */
    public static function rejected(string $modul, string $judul, ?string $detail = null, $target = null): LogAktivitas
    {
        return self::log('reject', $modul, $judul, $detail, $target);
    }
}
