<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $fillable = [
        'user_id', 'tipe_file', 'nama_file', 'jumlah_baris',
        'jumlah_berhasil', 'jumlah_gagal', 'keterangan', 'imported_at',
    ];

    protected $casts = ['imported_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getJenisImportLabelAttribute(): string
    {
        $tipeFile = $this->tipe_file;

        if (! $tipeFile && $this->keterangan) {
            $tipeFile = str_contains($this->keterangan, 'status_komitmen')
                ? 'status_komitmen'
                : (str_contains($this->keterangan, 'rekap_pirt') ? 'rekap_pirt' : null);
        }

        return match ($tipeFile) {
            'rekap_pirt' => 'Rekap Data PIRT',
            'status_komitmen' => 'Status Pemenuhan Komitmen',
            default => 'Tidak diketahui',
        };
    }
}
