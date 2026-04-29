<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi (Mass Assignment).
     */
    protected $fillable = [
        'name', 
        'email', 
        'nip', 
        'password', 
        'tempat_lahir', 
        'tanggal_lahir',
        'jenis_kelamin', 
        'nomor_hp', 
        'alamat', 
        'foto_profil', 
        'departemen_id',
        'role', 
        'status_kerja', // REVISI: Menggantikan is_active
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi JSON.
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * Atribut casting untuk konsistensi data.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tanggal_lahir' => 'date:Y-m-d', 
        // REVISI: is_active boolean dihapus karena sekarang menggunakan string status_kerja
    ];

    /**
     * Menambahkan atribut virtual (Accessor) ke dalam response JSON.
     */
    protected $appends = [
        'jenis_kelamin_lengkap',
        'foto_profil_url',
    ];

    /**
     * ACCESSOR: Foto Profil URL
     */
    protected function fotoProfilUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->foto_profil) {
                    return asset('storage/' . $this->foto_profil);
                }
                return asset('img/default-user.png');
            }
        );
    }

    /**
     * ACCESSOR: Label Jenis Kelamin
     */
    public function jenisKelaminLengkap(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->jenis_kelamin === 'L' ? 'Laki-laki' : ($this->jenis_kelamin === 'P' ? 'Perempuan' : null)
        );
    }

    /* ==========================================================================
    RELASI DATABASE
    ========================================================================== */

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function izins(): HasMany
    {
        return $this->hasMany(Izin::class);
    }

    public function peminjamanInventaris(): HasMany
    {
        return $this->hasMany(PeminjamanInventaris::class);
    }
}