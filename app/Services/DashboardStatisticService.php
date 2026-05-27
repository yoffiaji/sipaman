<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AuditTrail;
use App\Models\ImportLog;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardStatisticService
{
    public function adminStats(): array
    {
        return [
            'produk' => Produk::count(),
            'terverifikasi' => Produk::where('is_verified', true)->count(),
            'belum_terverifikasi' => Produk::where('is_verified', false)->count(),
            'umkm' => Produk::query()
                ->select('nama_pelaku_usaha')
                ->whereNotNull('nama_pelaku_usaha')
                ->distinct()
                ->count('nama_pelaku_usaha'),
            'hampir_expired' => Produk::whereNotNull('masa_berlaku_pirt')
                ->whereDate('masa_berlaku_pirt', '<=', now()->addMonths(6))
                ->count(),
            'import_terakhir' => ImportLog::with('user')->latest('imported_at')->first(),
            'produk_terbaru' => Produk::latest()->limit(5)->get(),
        ];
    }

    public function superAdminStats(): array
    {
        return [
            'users' => User::count(),
            'admin' => User::whereHas('role', fn ($query) => $query->where('nama_role', 'admin'))->count(),
            'audit' => AuditTrail::count(),
            'activity' => ActivityLog::count(),
        ];
    }
}
