<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori'; 
    protected $primaryKey = 'idkategori'; 
    public $timestamps = false; 
    protected $fillable = ['nama_kategori']; 

    // Relasi: Satu kategori memiliki banyak buku [cite: 31]
    public function buku()
    {
        return $this->hasMany(Buku::class, 'idkategori', 'idkategori');
    }
}