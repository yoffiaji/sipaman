<?php

namespace App\Support;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SystemSettings
{
    private const CACHE_KEY = 'system_settings.public';

    public static function all(): array
    {
        try {
            if (! Schema::hasTable('system_settings')) {
                return [];
            }
        } catch (\Throwable) {
            return [];
        }

        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return self::fresh();
            });
        } catch (\Throwable) {
            return self::fresh();
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = self::all();

        return ($settings[$key] ?? null) ?: $default;
    }

    public static function forget(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable) {
        }
    }

    private static function fresh(): array
    {
        try {
            return SystemSetting::query()
                ->pluck('value', 'key')
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
