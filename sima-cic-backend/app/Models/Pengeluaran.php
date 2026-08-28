<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'akun_id',
        'nama_pengeluaran',
        'kategori',
        'nominal',
        'tanggal_pengeluaran',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'float',
        'tanggal_pengeluaran' => 'date:Y-m-d',
    ];

    /**
     * Relasi ke Chart of Accounts (Akun)
     */
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}
