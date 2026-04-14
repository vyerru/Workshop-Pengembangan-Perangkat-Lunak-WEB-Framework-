<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = "penjualans";
    protected $primaryKey = 'id_penjualan'; 
    protected $fillable = ["timestamp", "total"]; 
    
    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }
}