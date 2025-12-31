<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanInventaris extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_inventaris';

    protected $fillable = [
        'user_id',
        'inventaris_id',
        'quantity', // Field baru untuk menyimpan jumlah barang yang dipinjam
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_pengembalian',
        'keterangan',
        'status',
        'approved_by_user_id',
        'alasan_penolakan',
    ];

    protected $casts = [
        'quantity'              => 'integer', // Cast field quantity
        'tanggal_mulai'         => 'date:Y-m-d',
        'tanggal_selesai'       => 'date:Y-m-d',
        'tanggal_pengembalian'  => 'datetime',
    ];

    // Relasi ke pemohon
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke barang inventaris
    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    // Relasi ke admin yang menyetujui
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}