<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $fillable = [
        'nama',
        'total',
        'status_bayar',
        'metode_bayar',
        'snap_token',
        'transaction_id',
    ];

    protected $casts = [
        'status_bayar' => 'integer',
        'total'        => 'integer',
    ];

    // Konstanta status untuk kemudahan
    const STATUS_PENDING  = 0;
    const STATUS_LUNAS    = 1;
    const STATUS_BATAL    = 2;

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }
}