<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\LandingPageContent;
use App\Models\Produk;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $contents = LandingPageContent::all()->keyBy('section_key');
        $featuredProducts = Produk::verified()->with(['kecamatan', 'gambarUtama'])->latest()->limit(6)->get();

        return view('public.home', compact('contents', 'featuredProducts'));
    }
}
