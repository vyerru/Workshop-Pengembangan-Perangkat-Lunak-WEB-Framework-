<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiKuliah extends Model
{
    protected $fillable = [
        'user_id',
        'topik_mingguan',
        'pertemuan_ke',
        'waktu_hadir',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
