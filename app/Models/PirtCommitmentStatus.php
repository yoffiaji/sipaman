<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PirtCommitmentStatus extends Model
{
    protected $fillable = [
        'produk_id',
        'no_sppirt',
        'provinsi',
        'kab_kota',
        'nama_pelaku_usaha',
        'alamat_usaha',
        'phone',
        'tanggal_terdaftar',
        'nib',
        'verifikasi_produk',
        'verifikasi_label',
        'pkp',
        'cppob_pemeriksaan_sarana',
        'status_pemenuhan_komitmen',
    ];

    protected $casts = [
        'tanggal_terdaftar' => 'date',
        'verifikasi_produk' => 'boolean',
        'verifikasi_label' => 'boolean',
        'pkp' => 'boolean',
        'cppob_pemeriksaan_sarana' => 'boolean',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
