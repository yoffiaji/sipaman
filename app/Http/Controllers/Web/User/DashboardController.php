<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $products = Produk::with(['kecamatan', 'gambarUtama', 'verifikasi'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.dashboard', compact('products'));
    }
}
