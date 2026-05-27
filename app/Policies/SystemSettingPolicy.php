<?php

namespace App\Policies;

use App\Models\SystemSetting;
use App\Models\User;

class SystemSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, SystemSetting $systemSetting): bool
    {
        return $user->hasRole('super_admin');
    }
}
