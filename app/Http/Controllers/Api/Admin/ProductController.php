<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Produk;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use LogsAuditTrail;

    public function index(Request $request): JsonResponse
    {
        $products = Produk::with(['kecamatan', 'jenisBarang', 'gambarUtama', 'verifikasi.verifikator', 'commitmentStatus'])
            ->search($request->query('search'))
            ->when($request->has('is_verified'), fn ($query) => $query->where('is_verified', filter_var($request->query('is_verified'), FILTER_VALIDATE_BOOLEAN)))
            ->latest()
            ->paginate($request->query('per_page', 20));

        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $produk = Produk::create($request->validated());
        $this->logAudit('create', 'produks', $produk->id, null, $produk->toArray());

        return response()->json(['message' => 'Produk berhasil ditambahkan.', 'data' => $produk], 201);
    }

    public function show(Produk $produk): JsonResponse
    {
        return response()->json(['data' => $produk->load(['kecamatan', 'jenisBarang', 'gambarProduks', 'verifikasi.verifikator', 'commitmentStatus'])]);
    }

    public function update(UpdateProductRequest $request, Produk $produk): JsonResponse
    {
        $before = $produk->toArray();
        $produk->update($request->validated());
        $this->logAudit('update', 'produks', $produk->id, $before, $produk->fresh()->toArray());

        return response()->json(['message' => 'Produk berhasil diperbarui.', 'data' => $produk->fresh(['kecamatan', 'jenisBarang'])]);
    }

    public function destroy(Produk $produk): JsonResponse
    {
        $before = $produk->toArray();
        foreach ($produk->gambarProduks as $gambar) {
            Storage::disk('public')->delete($gambar->url_gambar);
        }
        $produk->delete();
        $this->logAudit('delete', 'produks', $before['id'], $before, null);

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}
