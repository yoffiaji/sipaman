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
        $before = $produk->gambarUtama?->toArray();
        $gambar = $this->productImageService->replaceOne($produk, $request->file('gambar'));
        $this->logAudit('update', 'gambar_produks', $produk->id, $before, $gambar->toArray());

        return response()->json(['message' => 'Gambar produk berhasil diganti.', 'data' => $gambar], 201);
    }

    public function destroy(GambarProduk $gambarProduk): JsonResponse
    {
        $before = $gambarProduk->toArray();
        $this->productImageService->delete($gambarProduk);
        $this->logAudit('delete', 'gambar_produks', $before['id'], $before, null);

        return response()->json(['message' => 'Gambar berhasil dihapus.']);
    }
}
