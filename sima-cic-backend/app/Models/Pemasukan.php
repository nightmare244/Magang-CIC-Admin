<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $fillable = [
        'akun_id',
        'nama_pemasukan',
        'tipe',
        'jumlah',
        'nominal',
        'tanggal_pemasukan',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'float',
        'jumlah' => 'integer',
        'tanggal_pemasukan' => 'date:Y-m-d',
    ];

    /**
     * Relasi ke Chart of Accounts (Akun)
     */
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}
