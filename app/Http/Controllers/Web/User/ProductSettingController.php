<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
use App\Http\Requests\User\UpdateProductSupportRequest;
use App\Models\Produk;
use App\Services\ProductImageService;
use App\Traits\LogsAuditTrail;
use Illuminate\Support\Facades\Auth;

class ProductSettingController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private ProductImageService $productImageService)
    {
    }

    public function index()
    {
        $produks = Produk::ownedBy(Auth::id())
            ->with(['gambarUtama', 'gambarProduks'])
            ->latest()
            ->get();

        return view('user.products.setting', compact('produks'));
    }

    public function edit(int $id)
    {
        $produk = $this->milikSaya($id);

        return view('user.products.setting-edit', compact('produk'));
    }

    public function update(UpdateProductSupportRequest $request, int $id)
    {
        $produk = $this->milikSaya($id);
        $before = $produk->only(['harga', 'deskripsi']);

        $produk->update($request->validated());

        $this->logAudit(
            'update',
            'produks',
            $produk->id,
            $before,
            $produk->fresh()->only(['harga', 'deskripsi'])
        );

        return back()->with('success', 'Informasi produk berhasil disimpan.');
    }

    public function uploadGambar(StoreProductImageRequest $request, int $id)
    {
        $produk = $this->milikSaya($id);

        $before = $produk->gambarUtama?->toArray();
        $gambar = $this->productImageService->replaceOne($produk, $request->file('gambar'));

        $this->logAudit('update', 'gambar_produks', $produk->id, $before, $gambar->toArray());

        return back()->with('success', 'Gambar produk berhasil diganti.');
    }

    private function milikSaya(int $id): Produk
    {
        return Produk::ownedBy(Auth::id())
            ->with(['gambarProduks', 'gambarUtama'])
            ->findOrFail($id);
    }
}
