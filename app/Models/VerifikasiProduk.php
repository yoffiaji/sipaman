<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerifikasiProduk extends Model
{
    protected $fillable = [
        'produk_id',
        'user_verifikator_id',
        'verifikasi_produk',
        'verifikasi_label',
        'pkp',
        'cppob_pemeriksaan_sarana',
        'status_komitmen',
        'catatan',
    ];

    protected $casts = [
        'verifikasi_produk'       => 'boolean',
        'verifikasi_label'        => 'boolean',
        'pkp'                     => 'boolean',
        'cppob_pemeriksaan_sarana'=> 'boolean',
        'status_komitmen'         => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_verifikator_id');
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Hitung status_komitmen dari 4 kolom verifikasi.
     * Semua harus true agar status_komitmen = true.
     */
    public static function hitungStatusKomitmen(
        bool $verifikasiProduk,
        bool $verifikasiLabel,
        bool $pkp,
        bool $cppob
    ): bool {
        return $verifikasiProduk && $verifikasiLabel && $pkp && $cppob;
    }
}
