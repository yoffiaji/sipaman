<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
use App\Models\Produk;
use App\Services\ProductImageService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductImageManagementController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private ProductImageService $productImageService)
    {
    }

    public function index(Request $request): View
    {
        $products = Produk::with(['gambarUtama', 'jenisBarang'])
            ->search($request->query('search'))
            ->when($request->query('image_status') === 'available', fn ($query) => $query->whereHas('gambarProduks'))
            ->when($request->query('image_status') === 'missing', fn ($query) => $query->whereDoesntHave('gambarProduks'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Produk::count(),
            'available' => Produk::whereHas('gambarProduks')->count(),
            'missing' => Produk::whereDoesntHave('gambarProduks')->count(),
        ];

        return view('admin.product-images.index', compact('products', 'stats'));
    }

    public function update(StoreProductImageRequest $request, Produk $produk): RedirectResponse
    {
        $before = $produk->gambarUtama?->toArray();
        $gambar = $this->productImageService->replaceOne($produk, $request->file('gambar'));

        $this->logAudit('update', 'gambar_produks', $produk->id, $before, $gambar->toArray());

        return back()->with('success', 'Gambar produk berhasil diganti.');
    }
}
