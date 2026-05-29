<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JenisBarangAlias extends Model
{
    protected $fillable = [
        'jenis_barang_id',
        'keyword',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function jenisBarang(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class);
    }
}
