<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail_Penjualan extends Model
{
    protected $table = "penjualan_details";
    protected $fillable = ["idpenjualan_detail", "id_penjualan", "id_barang", "jumlah", "subtotal"];
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}
