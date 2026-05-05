<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumumanRead extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_reads';

    protected $fillable = [
        'user_id',       // ID Personel (User Node)
        'pengumuman_id', // ID Pengumuman terkait
        'read_at',       // Timestamp verifikasi baca
    ];

    // Konfigurasi casting untuk presisi data waktu
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Relasi: Log baca ini merujuk ke personil tertentu.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi: Log baca ini merujuk ke satu Pengumuman induk.
     */
    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }
}