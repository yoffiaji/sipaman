<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateSystemSettingRequest;
use App\Models\SystemSetting;
use App\Support\SystemSettings;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    use LogsAuditTrail;

    public function index(): View
    {
        $settings = SystemSetting::orderBy('key')->get();

        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(UpdateSystemSettingRequest $request, SystemSetting $setting): RedirectResponse
    {
        $before = $setting->toArray();
        $setting->update($request->validated());
        SystemSettings::forget();
        $this->logAudit('update', 'system_settings', $setting->id, $before, $setting->fresh()->toArray());

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
