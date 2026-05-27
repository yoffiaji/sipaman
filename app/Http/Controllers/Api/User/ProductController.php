<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    use LogsAuditTrail;

    private const PROTECTED_FIELDS = [
        'no_sppirt',
        'nib',
        'kategori_pangan',
        'jenis_pangan',
        'kemasan',
        'cara_penyimpanan',
        'wilayah',
        'is_verified',
        'tanggal_verifikasi',
        'masa_berlaku_pirt',
        'tanggal_pengajuan',
        'status_oss',
        'kecamatan_id',
        'jenis_barang_id',
        'nama_pelaku_usaha',
        'no_hp',
        'user_id',
    ];

    public function index(Request $request): JsonResponse
    {
        $produks = Produk::ownedBy($request->user()->id)
            ->with(['kecamatan', 'jenisBarang', 'gambarUtama'])
            ->orderBy('nama_branding')
            ->paginate($request->query('per_page', 15))
            ->through(fn (Produk $produk) => $this->formatOwnedProduct($produk));

        return response()->json($produks);
    }

    public function show(Request $request, Produk $produk): JsonResponse
    {
        if (! $this->isOwnedByAuthenticatedUser($produk, $request)) {
            return response()->json(['message' => 'Produk tidak ditemukan di akun Anda.'], 404);
        }

        $produk->load(['kecamatan', 'jenisBarang', 'gambarProduks']);

        return response()->json(['data' => $this->formatOwnedProduct($produk, true)]);
    }

    public function update(Request $request, Produk $produk): JsonResponse
    {
        if (! $this->isOwnedByAuthenticatedUser($produk, $request)) {
            return response()->json(['message' => 'Produk tidak ditemukan di akun Anda.'], 404);
        }

        $blockedFields = array_values(array_intersect(self::PROTECTED_FIELDS, array_keys($request->all())));

        if ($blockedFields !== []) {
            return response()->json([
                'message' => 'User hanya boleh mengubah data pendukung produk.',
                'field_dilarang' => $blockedFields,
            ], 422);
        }

        if ($request->has('alamat')) {
            $request->merge(['alamat_toko' => $request->input('alamat')]);
        }

        $data = $request->validate([
            'nama_toko' => 'sometimes|nullable|string|max:150',
            'alamat_toko' => 'sometimes|nullable|string|max:1000',
            'harga' => 'sometimes|nullable|integer|min:0|max:1000000000',
            'deskripsi' => 'sometimes|nullable|string|max:2000',
        ]);

        $sebelum = $produk->only(['nama_toko', 'alamat_toko', 'harga', 'deskripsi']);
        $produk->update($data);

        $this->logAudit(
            'update',
            'produks',
            $produk->id,
            $sebelum,
            $produk->fresh()->only(['nama_toko', 'alamat_toko', 'harga', 'deskripsi'])
        );

        return response()->json([
            'message' => 'Data pendukung produk berhasil diperbarui.',
            'data' => $this->formatOwnedProduct(
                $produk->fresh()->load(['kecamatan', 'jenisBarang', 'gambarProduks']),
                true
            ),
        ]);
    }

    private function isOwnedByAuthenticatedUser(Produk $produk, Request $request): bool
    {
        return (int) $produk->user_id === (int) $request->user()->id;
    }

    private function formatOwnedProduct(Produk $produk, bool $includeDetail = false): array
    {
        $masaBerlaku = $produk->masa_berlaku_pirt;

        $data = [
            'id' => $produk->id,
            'no_sppirt' => $produk->no_sppirt,
            'nama_branding' => $produk->nama_branding,
            'nama_toko' => $produk->nama_toko,
            'kategori_pangan' => $produk->kategori_pangan,
            'jenis_pangan' => $produk->jenis_pangan,
            'kemasan' => $produk->kemasan,
            'cara_penyimpanan' => $produk->cara_penyimpanan,
            'wilayah' => $produk->wilayah,
            'alamat_toko' => $produk->alamat_toko,
            'harga' => $produk->harga,
            'deskripsi' => $produk->deskripsi,
            'is_verified' => $produk->is_verified,
            'status_verifikasi' => $produk->is_verified ? 'terverifikasi' : 'belum_terverifikasi',
            'tanggal_verifikasi' => optional($produk->tanggal_verifikasi)->toDateString(),
            'masa_berlaku_pirt' => optional($masaBerlaku)->toDateString(),
            'pirt_hampir_expired' => $masaBerlaku
                ? Carbon::parse($masaBerlaku)->betweenIncluded(now(), now()->addMonths(6))
                : false,
            'kecamatan' => $produk->kecamatan,
            'jenis_barang' => $produk->jenisBarang,
            'gambar_utama' => $produk->gambarUtama,
        ];

        if ($includeDetail) {
            $data['gambar_produk'] = $produk->gambarProduks;
        }

        return $data;
    }
}
