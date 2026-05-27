<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Produk extends Model
{
    protected $fillable = [
        'user_id',
        'no_sppirt',
        'nama_branding',
        'kategori_pangan',
        'jenis_pangan',
        'kemasan',
        'cara_penyimpanan',
        'wilayah',
        'kecamatan_id',
        'jenis_barang_id',
        'nama_pelaku_usaha',
        'alamat',
        'nib',
        'no_hp',
        'nama_toko',
        'alamat_toko',
        'harga',
        'deskripsi',
        'tanggal_pengajuan',
        'tanggal_verifikasi',
        'masa_berlaku_pirt',
        'status_oss',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'tanggal_pengajuan' => 'date',
        'tanggal_verifikasi' => 'date',
        'masa_berlaku_pirt' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function jenisBarang(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gambarProduks(): HasMany
    {
        return $this->hasMany(GambarProduk::class);
    }

    /** Gambar utama untuk tampilan katalog. Prioritaskan is_primary, fallback ke gambar terbaru. */
    public function gambarUtama(): HasOne
    {
        return $this->hasOne(GambarProduk::class)
            ->ofMany([
                'is_primary' => 'max',
                'uploaded_at' => 'max',
            ]);
    }

    public function verifikasi(): HasOne
    {
        return $this->hasOne(VerifikasiProduk::class);
    }

    public function commitmentStatus(): HasOne
    {
        return $this->hasOne(PirtCommitmentStatus::class);
    }

    // ── Query Scopes ──────────────────────────────────────────

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByKecamatan($query, $kecamatanId)
    {
        return $kecamatanId ? $query->where('kecamatan_id', $kecamatanId) : $query;
    }

    public function scopeByJenisBarang($query, $jenisBarangId)
    {
        return $jenisBarangId ? $query->where('jenis_barang_id', $jenisBarangId) : $query;
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('nama_branding', 'like', "%{$keyword}%")
                ->orWhere('nama_pelaku_usaha', 'like', "%{$keyword}%")
                ->orWhere('no_sppirt', 'like', "%{$keyword}%")
                ->orWhere('kategori_pangan', 'like', "%{$keyword}%")
                ->orWhere('jenis_pangan', 'like', "%{$keyword}%")
                ->orWhere('wilayah', 'like', "%{$keyword}%");
        });
    }
}
