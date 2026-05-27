<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\Produk;
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
            ->paginate(12)
            ->withQueryString();

        return view('public.umkm.index', compact('umkms'));
    }

    public function show(string $namaPelakuUsaha): View
    {
        $nama = str_replace('-', ' ', $namaPelakuUsaha);
        $products = Produk::verified()
            ->with(['kecamatan', 'gambarUtama'])
            ->whereRaw('LOWER(nama_pelaku_usaha) = ?', [mb_strtolower($nama)])
            ->paginate(12);

        abort_if($products->isEmpty(), 404);

        $umkm = $products->first();
        $slug = Str::slug($nama);

        return view('public.umkm.show', compact('nama', 'products', 'umkm', 'slug'));
    }
}
