<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'idbuku'; 
    protected $fillable = ['kode', 'judul', 'pengarang', 'idkategori'];
    public $timestamps = false;
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idkategori', 'idkategori');
    }
}