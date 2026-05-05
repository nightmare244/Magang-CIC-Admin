<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute; // Diperlukan untuk accessor
use Illuminate\Support\Facades\Storage; // Diperlukan untuk accessor
use Illuminate\Support\Str;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'quantity',
        'harga_satuan',
        'nilai_barang',
        'qr_code_string',
        'status_ketersediaan',
        'foto_barang',
    ];

    protected $casts = [
        'harga_satuan'          => 'decimal:2',
        'nilai_barang'          => 'decimal:2',
        'status_ketersediaan'   => 'string',
        'quantity'              => 'integer',
    ];

    protected $appends = [
        'foto_barang_url',
    ];

    // ----------------------------------------------------------------
    // ACCESSORS
    // ----------------------------------------------------------------

    /**
     * Accessor untuk mendapatkan URL lengkap foto barang.
     */
    protected function fotoBarangUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->foto_barang && Storage::disk('public')->exists($this->foto_barang)) {
                    // Menggunakan Storage::url untuk mendapatkan URL publik
                    return Storage::url($this->foto_barang);
                }
                // Fallback placeholder
                return '/default-inventaris.png'; 
            }
        );
    }


    protected static function boot()
    {
        parent::boot();

        // ----------------------------------------------------------------
        // Generate kode barang otomatis dan QR Code sebelum dibuat
        // ----------------------------------------------------------------
        static::creating(function ($model) {
            $latestId = static::max('id') ?? 0;
            $nextId = $latestId + 1;

            if (!$model->kode_barang) {
                $model->kode_barang = 'INV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }

            if (!$model->qr_code_string) {
                // Menambahkan timestamp agar sangat unik, mencegah konflik QR code
                $model->qr_code_string = $model->kode_barang . '-' . time() . '-' . Str::random(5);
            }
        });

        // ----------------------------------------------------------------
        // Hitung nilai barang otomatis sebelum disimpan (saving: create/update)
        // ----------------------------------------------------------------
        static::saving(function ($model) {
            $harga = floatval($model->harga_satuan ?? 0);
            $qty   = intval($model->quantity ?? 1);

            // Nilai barang dihitung dari harga_satuan * quantity
            $model->nilai_barang = $harga * $qty; 
        });

    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(PeminjamanInventaris::class, 'inventaris_id');
    }
}