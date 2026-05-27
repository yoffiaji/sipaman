<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
use App\Models\GambarProduk;
use App\Models\Produk;
use App\Services\ProductImageService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private ProductImageService $productImageService)
    {
    }

    public function store(StoreProductImageRequest $request, Produk $produk): JsonResponse
    {
        if (! $produk->is_verified) {
            return response()->json(['message' => 'Gambar hanya boleh ditambahkan pada produk yang sudah terverifikasi.'], 422);
        }

        $uploaded = $this->productImageService->storeMany($produk, $request->file('images'), (int) $request->input('primary_index', 0));
        $this->logAudit('update', 'gambar_produks', $produk->id, null, ['jumlah_upload' => count($uploaded)]);

        return response()->json(['message' => count($uploaded) . ' gambar berhasil diunggah.', 'data' => $uploaded], 201);
    }

    public function destroy(GambarProduk $gambarProduk): JsonResponse
    {
        $before = $gambarProduk->toArray();
        $this->productImageService->delete($gambarProduk);
        $this->logAudit('delete', 'gambar_produks', $before['id'], $before, null);

        return response()->json(['message' => 'Gambar berhasil dihapus.']);
    }
}
