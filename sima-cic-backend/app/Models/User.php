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
        'is_active',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi JSON.
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * PERBAIKAN STRATEGIS: 
     * Menggunakan format 'date:Y-m-d' memastikan data yang dikirim ke Vue 
     * adalah string murni. Ini mencegah browser menarik mundur tanggal 
     * akibat perbedaan zona waktu (WIB vs UTC).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tanggal_lahir' => 'date:Y-m-d', 
        'is_active' => 'boolean',
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
     * Menghasilkan URL lengkap untuk ditampilkan di tag <img> pada Vue.
     */
    protected function fotoProfilUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->foto_profil) {
                    // Mengambil file dari folder storage/public
                    return asset('storage/' . $this->foto_profil);
                }
                // Gambar default jika foto tidak ada
                return asset('img/default-user.png');
            }
        );
    }

    /**
     * ACCESSOR: Label Jenis Kelamin
     * Mengubah inisial L/P menjadi teks lengkap.
     */
    public function jenisKelaminLengkap(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->jenis_kelamin === 'L' ? 'Laki-laki' : ($this->jenis_kelamin === 'P' ? 'Perempuan' : null)
        );
    }

    /* ==========================================================================
    RELASI DATABASE
    ==========================================================================
    */

    /**
     * Relasi ke tabel Departemen.
     */
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    /**
     * Relasi ke data Absensi.
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Relasi ke data Izin.
     */
    public function izins(): HasMany
    {
        return $this->hasMany(Izin::class);
    }

    /**
     * Relasi ke data Peminjaman Inventaris.
     */
    public function peminjamanInventaris(): HasMany
    {
        return $this->hasMany(PeminjamanInventaris::class);
    }
}