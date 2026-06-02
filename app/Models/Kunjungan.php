<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kunjungan extends Model
{
    protected $table = 'kunjungans';

    protected $fillable = [
        'toko_id',
        'sales_id',
        'latitude_sales',
        'longitude_sales',
        'accuracy_sales',
        'latitude_toko',
        'longitude_toko',
        'accuracy_toko',
        'jarak_terhitung',
        'threshold_efektif',
        'status',
        'waktu_kunjungan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_kunjungan' => 'datetime',
        ];
    }

    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }
}
