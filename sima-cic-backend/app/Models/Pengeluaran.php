<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $fillable = [
        'nama_pengeluaran',
        'kategori',
        'nominal',
        'tanggal_pengeluaran',
        'keterangan',
    ];
}
