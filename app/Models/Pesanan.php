<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $fillable = [
        'user_id',
        'nama', 
        'vendor_id', 
        'kode_pesanan', 
        'qr_token',
        'total', 
        'status_bayar', 
        'metode_bayar', 
        'snap_token',
        'transaction_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

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