<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\VerifikasiProduk;
use Illuminate\Support\Facades\DB;

class ProductVerificationService
{
    public function update(Produk $produk, array $data): VerifikasiProduk
    {
        return DB::transaction(function () use ($produk, $data) {
            $statusKomitmen = VerifikasiProduk::hitungStatusKomitmen(
                (bool) ($data['verifikasi_produk'] ?? false),
                (bool) ($data['verifikasi_label'] ?? false),
                (bool) ($data['pkp'] ?? false),
                (bool) ($data['cppob_pemeriksaan_sarana'] ?? false)
            );

            $verifikasi = VerifikasiProduk::updateOrCreate(
                ['produk_id' => $produk->id],
                [
                    'user_verifikator_id' => auth()->id(),
                    'verifikasi_produk' => (bool) ($data['verifikasi_produk'] ?? false),
                    'verifikasi_label' => (bool) ($data['verifikasi_label'] ?? false),
                    'pkp' => (bool) ($data['pkp'] ?? false),
                    'cppob_pemeriksaan_sarana' => (bool) ($data['cppob_pemeriksaan_sarana'] ?? false),
                    'status_komitmen' => $statusKomitmen,
                    'catatan' => $data['catatan'] ?? null,
                ]
            );

            $produk->update([
                'is_verified' => $statusKomitmen,
                'tanggal_verifikasi' => $statusKomitmen ? ($produk->tanggal_verifikasi ?? now()->toDateString()) : null,
                'masa_berlaku_pirt' => $statusKomitmen ? ($produk->masa_berlaku_pirt ?? now()->addYears(5)->toDateString()) : null,
            ]);

            return $verifikasi->fresh(['produk', 'verifikator']);
        });
    }

    public function reject(Produk $produk, ?string $catatan = null): VerifikasiProduk
    {
        return $this->update($produk, [
            'verifikasi_produk' => false,
            'verifikasi_label' => false,
            'pkp' => false,
            'cppob_pemeriksaan_sarana' => false,
            'catatan' => $catatan,
        ]);
    }
}
