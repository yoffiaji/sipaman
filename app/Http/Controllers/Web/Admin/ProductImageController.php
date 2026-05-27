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
        if (! $produk->is_verified) {
            return back()->withErrors(['images' => 'Gambar hanya boleh ditambahkan pada produk yang sudah terverifikasi.']);
        }

        $uploaded = $this->productImageService->storeMany(
            $produk,
            $request->file('images'),
            (int) $request->input('primary_index', 0)
        );

        $this->logAudit('update', 'gambar_produks', $produk->id, null, ['jumlah_upload' => count($uploaded)]);

        return back()->with('success', count($uploaded) . ' gambar berhasil diunggah.');
    }

    public function destroy(GambarProduk $gambarProduk): RedirectResponse
    {
        $produkId = $gambarProduk->produk_id;
        $before = $gambarProduk->toArray();

        $this->productImageService->delete($gambarProduk);
        $this->logAudit('delete', 'gambar_produks', $before['id'], $before, null);

        return redirect()->route('admin.products.show', $produkId)->with('success', 'Gambar berhasil dihapus.');
    }
}
