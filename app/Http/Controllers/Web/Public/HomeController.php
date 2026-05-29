<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\LandingPageContent;
use App\Models\Produk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $contents = Schema::hasTable('landing_page_contents')
            ? LandingPageContent::all()->keyBy('section_key')
            : collect();

        $featuredProducts = Schema::hasTable('produks')
            ? Produk::verified()->with(['kecamatan', 'gambarUtama'])->latest()->limit(6)->get()
            : new Collection();

        $homeStats = Schema::hasTable('produks')
            ? [
                'verified_products' => Produk::verified()->count(),
                'districts' => 17,
                'umkm' => Produk::verified()
                    ->whereNotNull('nama_pelaku_usaha')
                    ->distinct('nama_pelaku_usaha')
                    ->count('nama_pelaku_usaha'),
            ]
            : [
                'verified_products' => 0,
                'districts' => 17,
                'umkm' => 0,
            ];

        return view('public.home', compact('contents', 'featuredProducts', 'homeStats'));
    }
}
