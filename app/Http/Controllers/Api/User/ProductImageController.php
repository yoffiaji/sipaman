<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
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
        if ((int) $produk->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Produk tidak ditemukan di akun Anda.'], 404);
        }

        $before = $produk->gambarUtama?->toArray();
        $gambar = $this->productImageService->replaceOne($produk, $request->file('gambar'));

        $this->logAudit('update', 'gambar_produks', $produk->id, $before, $gambar->toArray());

        return response()->json([
            'message' => 'Gambar produk berhasil diganti.',
            'data' => $gambar,
        ], 201);
    }
}
