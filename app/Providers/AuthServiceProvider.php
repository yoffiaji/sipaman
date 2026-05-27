<?php

namespace App\Providers;

use App\Models\AuditTrail;
use App\Models\Produk;
use App\Models\SystemSetting;
use App\Models\User;
use App\Policies\AuditTrailPolicy;
use App\Policies\ProdukPolicy;
use App\Policies\SystemSettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Produk::class => ProdukPolicy::class,
        User::class => UserPolicy::class,
        AuditTrail::class => AuditTrailPolicy::class,
        SystemSetting::class => SystemSettingPolicy::class,
    ];

    public function boot(): void
    {
        // Policies registered through $policies.
    }
}
