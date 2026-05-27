<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');
        $produkQuery = Produk::ownedBy($user->id);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role->nama_role ?? null,
                'status_akun' => $user->status_akun,
            ],
            'ringkasan' => [
                'jumlah_produk' => (clone $produkQuery)->count(),
                'produk_terverifikasi' => (clone $produkQuery)->where('is_verified', true)->count(),
                'produk_hampir_expired' => (clone $produkQuery)
                    ->whereNotNull('masa_berlaku_pirt')
                    ->whereDate('masa_berlaku_pirt', '>=', now())
                    ->whereDate('masa_berlaku_pirt', '<=', now()->addMonths(6))
                    ->count(),
            ],
        ]);
    }
}
