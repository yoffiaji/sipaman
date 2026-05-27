<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\GambarProduk;
use App\Models\Produk;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    use LogsAuditTrail;

    public function store(Request $request, Produk $produk): JsonResponse
    {
        if (! $this->isOwnedByAuthenticatedUser($produk, $request)) {
            return response()->json(['message' => 'Produk tidak ditemukan di akun Anda.'], 404);
        }

        if (! $produk->is_verified) {
            return response()->json([
                'message' => 'Gambar hanya boleh ditambahkan pada produk yang sudah terverifikasi.',
            ], 422);
        }

        $request->validate([
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'primary_index' => 'nullable|integer|min:0',
        ]);

        $primaryIdx = (int) ($request->primary_index ?? 0);
        $uploaded = [];

        foreach ($request->file('images') as $idx => $file) {
            $path = $file->store("produk/{$produk->id}", 'public');
            $isPrimary = $idx === $primaryIdx;

            if ($isPrimary) {
                GambarProduk::where('produk_id', $produk->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $uploaded[] = GambarProduk::create([
                'produk_id' => $produk->id,
                'url_gambar' => $path,
                'is_primary' => $isPrimary,
            ]);
        }

        $this->logAudit('update', 'gambar_produks', $produk->id, null, [
            'jumlah_upload' => count($uploaded),
        ]);

        return response()->json([
            'message' => count($uploaded).' gambar produk berhasil diunggah.',
            'data' => $uploaded,
        ], 201);
    }

    public function destroy(Request $request, Produk $produk): JsonResponse
    {
        if (! $this->isOwnedByAuthenticatedUser($produk, $request)) {
            return response()->json(['message' => 'Produk tidak ditemukan di akun Anda.'], 404);
        }

        $request->validate([
            'gambar_id' => 'required|integer|exists:gambar_produks,id',
        ]);

        $gambar = GambarProduk::where('id', $request->gambar_id)
            ->where('produk_id', $produk->id)
            ->firstOrFail();

        Storage::disk('public')->delete($gambar->url_gambar);
        $gambar->delete();

        $this->logAudit('delete', 'gambar_produks', $gambar->id, [
            'url_gambar' => $gambar->url_gambar,
        ], null);

        return response()->json(['message' => 'Gambar produk berhasil dihapus.']);
    }

    private function isOwnedByAuthenticatedUser(Produk $produk, Request $request): bool
    {
        return (int) $produk->user_id === (int) $request->user()->id;
    }
}
