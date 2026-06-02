<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Support\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UmkmController extends Controller
{
    public function index(Request $request): View
    {
        $umkms = Produk::verified()
            ->selectRaw('nama_pelaku_usaha, MIN(alamat) as alamat, MIN(wilayah) as wilayah, COUNT(*) as total_produk')
            ->when($request->filled('search'), function ($query) use ($request) {
                $keyword = $request->query('search');
                $query->where('nama_pelaku_usaha', 'like', "%{$keyword}%");
            })
            ->whereNotNull('nama_pelaku_usaha')
            ->groupBy('nama_pelaku_usaha')
            ->orderBy('nama_pelaku_usaha')
            ->paginate(SystemSettings::pagination())
            ->withQueryString();

        return view('public.umkm.index', compact('umkms'));
    }

    public function show(string $namaPelakuUsaha): View
    {
        $nama = $this->resolveNamaPelakuUsaha($namaPelakuUsaha);
        abort_unless($nama, 404);

        $products = Produk::verified()
            ->with(['kecamatan', 'gambarUtama'])
            ->whereRaw('LOWER(nama_pelaku_usaha) = ?', [mb_strtolower($nama)])
            ->paginate(SystemSettings::pagination())
            ->withQueryString();

        abort_if($products->isEmpty(), 404);

        $umkm = $products->first();
        $slug = Str::slug($nama);

        return view('public.umkm.show', compact('nama', 'products', 'umkm', 'slug'));
    }

    private function resolveNamaPelakuUsaha(string $routeValue): ?string
    {
        $decoded = trim(rawurldecode($routeValue));

        if ($this->pelakuUsahaExists($decoded)) {
            return $decoded;
        }

        $slug = Str::slug($decoded);

        return Produk::verified()
            ->whereNotNull('nama_pelaku_usaha')
            ->select('nama_pelaku_usaha')
            ->distinct()
            ->pluck('nama_pelaku_usaha')
            ->first(fn (string $nama) => Str::slug($nama) === $slug);
    }

    private function pelakuUsahaExists(string $nama): bool
    {
        if ($nama === '') {
            return false;
        }

        return Produk::verified()
            ->whereRaw('LOWER(nama_pelaku_usaha) = ?', [mb_strtolower($nama)])
            ->exists();
    }
}
