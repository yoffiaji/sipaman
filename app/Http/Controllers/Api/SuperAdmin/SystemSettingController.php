<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateSystemSettingRequest;
use App\Models\SystemSetting;
use App\Support\SystemSettings;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;

class SystemSettingController extends Controller
{
    use LogsAuditTrail;

    public function index(): JsonResponse
    {
        return response()->json(['data' => SystemSetting::orderBy('key')->get()]);
    }

    public function update(UpdateSystemSettingRequest $request, SystemSetting $setting): JsonResponse
    {
        $before = $setting->toArray();
        $setting->update($request->validated());
        SystemSettings::forget();
        $this->logAudit('update', 'system_settings', $setting->id, $before, $setting->fresh()->toArray());

        return response()->json(['message' => 'Pengaturan sistem berhasil diperbarui.', 'data' => $setting->fresh()]);
    }
}
