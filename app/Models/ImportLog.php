<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $fillable = [
        'user_id', 'nama_file', 'jumlah_baris',
        'jumlah_berhasil', 'jumlah_gagal', 'keterangan', 'imported_at',
    ];

    protected $casts = ['imported_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
