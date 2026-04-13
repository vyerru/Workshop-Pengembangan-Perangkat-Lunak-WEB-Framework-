<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = "penjualans";
    protected $fillable = ["id_penjualan", "timestamp", "total"];

    public function penjualan()
    {
        return $this->hasMany(Detail_Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}
