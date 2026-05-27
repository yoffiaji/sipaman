<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PERBAIKAN AUDIT #4: Tambah accessor gambar_url
 * -----------------------------------------------
 * Frontend tidak perlu lagi manual gabungkan base URL.
 * Cukup gunakan: gambar.gambar_url
 * Output: http://127.0.0.1:8000/storage/produk/1/xxx.jpg
 */
class GambarProduk extends Model
{
    protected $fillable = ['produk_id', 'url_gambar', 'is_primary', 'uploaded_at'];

    protected $casts = [
        'is_primary'  => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    // Append gambar_url ke setiap response JSON secara otomatis
    protected $appends = ['gambar_url'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Accessor: URL lengkap gambar yang siap dipakai langsung di frontend.
     * Frontend cukup akses: gambar.gambar_url
     */
    public function getGambarUrlAttribute(): string
    {
        return url('storage/' . $this->url_gambar);
    }
}
