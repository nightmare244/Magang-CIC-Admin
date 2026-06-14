<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'aksi',
        'modul',
        'judul',
        'detail',
        'target_id',
        'target_type',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User (bisa null kalau user sudah dihapus)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
