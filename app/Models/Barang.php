<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $fillable = ['nama', 'harga', 'timestamp'];
    public $timestamps = false;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Barang $barang) {
            if (empty($barang->id_barang)) {
                $count = static::whereDate('timestamp', today())->count() + 1;
                $barang->id_barang = now()->format('ymd') . str_pad($count, 2, '0', STR_PAD_LEFT);
            }
        });
    }
}