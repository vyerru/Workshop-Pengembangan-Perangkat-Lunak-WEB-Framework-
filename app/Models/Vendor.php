<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = ['user_id', 'nama_vendor'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'vendor_id');
    }
}