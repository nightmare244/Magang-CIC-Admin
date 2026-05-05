<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lokasi_masuk',
        'lokasi_pulang',
        'status_masuk',
        'status_hari',
        'foto_checkin',
        'foto_checkout',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor foto_checkin (URL)
     */
    public function getFotoCheckinUrlAttribute()
    {
        return $this->foto_checkin
            ? asset('storage/' . $this->foto_checkin)
            : null;
    }

    /**
     * Accessor foto_checkout (URL)
     */
    public function getFotoCheckoutUrlAttribute()
    {
        return $this->foto_checkout
            ? asset('storage/' . $this->foto_checkout)
            : null;
    }
}
