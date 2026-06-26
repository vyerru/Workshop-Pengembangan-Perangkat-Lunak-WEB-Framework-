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
        'transaction_id',
        'nomor_antrian',
        'status_antrian',
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
        'status_bayar'  => 'integer',
        'total'         => 'integer',
        'nomor_antrian' => 'integer',
    ];

    // Konstanta status pembayaran
    const STATUS_PENDING  = 0;
    const STATUS_LUNAS    = 1;
    const STATUS_BATAL    = 2;

    // Konstanta status antrian
    const ANTRIAN_PENDING        = 'pending';
    const ANTRIAN_DIPROSES       = 'diproses';
    const ANTRIAN_SIAP_DIPANGGIL = 'siap_dipanggil';
    const ANTRIAN_SELESAI        = 'selesai';

    public function scopeForVendorToday($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId)
            ->whereBetween('created_at', [today()->startOfDay(), today()->endOfDay()]);
    }

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }
}