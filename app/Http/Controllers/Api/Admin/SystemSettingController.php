<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SystemSettingController
 * -----------------------
 * Akses: super_admin only
 * GET /api/super-admin/settings        → Daftar semua konfigurasi sistem
 * PUT /api/super-admin/settings/{key}  → Update nilai konfigurasi
 */
class SystemSettingController extends Controller
{
    use LogsAuditTrail;

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => SystemSetting::orderBy('key')->get(),
        ]);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $data = $request->validate([
            'value'     => 'required|string|max:1000',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $setting = SystemSetting::firstOrNew(['key' => $key]);
        $sebelum = $setting->exists ? $setting->toArray() : null;

        $setting->fill([
            'value'     => $data['value'],
            'deskripsi' => $data['deskripsi'] ?? $setting->deskripsi,
        ]);
        $setting->save();

        $this->logAudit('update', 'system_settings', $setting->id, $sebelum, $setting->toArray());

        return response()->json([
            'message' => "Konfigurasi '{$key}' berhasil diperbarui.",
            'data'    => $setting->fresh(),
        ]);
    }
}
