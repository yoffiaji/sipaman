<?php

namespace App\Services;

use App\Models\GambarProduk;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function storeMany(Produk $produk, array $files, int $primaryIndex = 0): array
    {
        return DB::transaction(function () use ($produk, $files, $primaryIndex) {
            $uploaded = [];

            foreach ($files as $index => $file) {
                $isPrimary = $index === $primaryIndex;

                if ($isPrimary) {
                    GambarProduk::where('produk_id', $produk->id)->update(['is_primary' => false]);
                }

                $uploaded[] = GambarProduk::create([
                    'produk_id' => $produk->id,
                    'url_gambar' => $file->store("produk/{$produk->id}", 'public'),
                    'is_primary' => $isPrimary,
                    'uploaded_at' => now(),
                ]);
            }

            if (! $produk->gambarProduks()->where('is_primary', true)->exists()) {
                $produk->gambarProduks()->oldest('uploaded_at')->first()?->update(['is_primary' => true]);
            }

            return $uploaded;
        });
    }

    public function delete(GambarProduk $gambarProduk): void
    {
        DB::transaction(function () use ($gambarProduk) {
            $produkId = $gambarProduk->produk_id;
            $wasPrimary = $gambarProduk->is_primary;
            $path = $gambarProduk->url_gambar;

            $gambarProduk->delete();
            Storage::disk('public')->delete($path);

            if ($wasPrimary) {
                GambarProduk::where('produk_id', $produkId)
                    ->oldest('uploaded_at')
                    ->first()?->update(['is_primary' => true]);
            }
        });
    }
}
