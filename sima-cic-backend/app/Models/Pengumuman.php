<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'user_id',
        'target_departemen_id',
        'nomor_surat',
        'judul',
        'isi',
        'file_path',
    ];

    /**
     * Relasi: Pengumuman dibuat oleh satu User (Admin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi: Pengumuman ditujukan ke Departemen tertentu (Opsional).
     */
    public function targetDepartemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'target_departemen_id');
    }

    /**
     * Relasi: Satu pengumuman memiliki banyak log baca.
     * Inilah relasi yang digunakan untuk membersihkan data sebelum penghapusan.
     */
    public function reads(): HasMany
    {
        return $this->hasMany(PengumumanRead::class, 'pengumuman_id');
    }
}