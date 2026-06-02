<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Toko extends Model
{
    /** @use HasFactory<\Database\Factories\TokoFactory> */
    use HasFactory;
    protected $table = 'tokos';

    protected $fillable = [
        'nama_toko',
        'alamat',
        'latitude',
        'longitude',
        'accuracy',
        'barcode_token',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Toko $toko) {
            if (empty($toko->barcode_token)) {
                $toko->barcode_token = Str::random(32);
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kunjungans(): HasMany
    {
        return $this->hasMany(Kunjungan::class);
    }
}
