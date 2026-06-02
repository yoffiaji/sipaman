<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
use App\Models\GambarProduk;
use App\Models\Produk;
use App\Services\ProductImageService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;

class ProductImageController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private ProductImageService $productImageService)
    {
    }

    public function store(StoreProductImageRequest $request, Produk $produk): RedirectResponse
    {
        $before = $produk->gambarUtama?->toArray();
        $gambar = $this->productImageService->replaceOne($produk, $request->file('gambar'));

        $this->logAudit('update', 'gambar_produks', $produk->id, $before, $gambar->toArray());

        return back()->with('success', 'Gambar produk berhasil diganti.');
    }

    public function destroy(GambarProduk $gambarProduk): RedirectResponse
    {
        $produkId = $gambarProduk->produk_id;
        $before = $gambarProduk->toArray();

        $this->productImageService->delete($gambarProduk);
        $this->logAudit('delete', 'gambar_produks', $before['id'], $before, null);

        return redirect()->route('panel.products.show', $produkId)->with('success', 'Gambar berhasil dihapus.');
    }
}
