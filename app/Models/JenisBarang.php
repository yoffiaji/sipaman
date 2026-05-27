<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBarang extends Model
{
    protected $fillable = ['nama_jenis'];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }
}
