<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemen extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi (Mass Assignment)
     */
    protected $fillable = [
        'nama_departemen',
        'deskripsi',
    ];

    /**
     * Casting otomatis untuk field tertentu (opsional).
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * Relasi: Satu Departemen punya banyak User (Karyawan)
     * 
     * Contoh:
     * $departemen->users; // Mengambil semua karyawan
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'departemen_id');
    }

    /**
     * Accessor opsional jika ingin mengubah format response
     */
    public function getLabelAttribute()
    {
        return strtoupper($this->nama_departemen);
    }
}
