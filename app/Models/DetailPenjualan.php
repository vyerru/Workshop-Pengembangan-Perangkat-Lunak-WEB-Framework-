<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = "penjualan_details";
    protected $primaryKey = 'idpenjualan_detail';
    protected $fillable = ["id_penjualan", "id_barang", "jumlah", "subtotal"];
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}
