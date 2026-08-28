<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kategori',
        'saldo_normal',
        'saldo_awal',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'saldo_awal' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to Pemasukan
     */
    public function pemasukans()
    {
        return $this->hasMany(Pemasukan::class, 'akun_id');
    }

    /**
     * Relationship to Pengeluaran
     */
    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'akun_id');
    }

    /**
     * Scope for active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
