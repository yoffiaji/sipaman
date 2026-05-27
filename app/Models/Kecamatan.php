<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $fillable = ['nama_kecamatan', 'kab_kota'];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }
}
