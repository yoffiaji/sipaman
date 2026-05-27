<?php

namespace App\Policies;

use App\Models\User;

class AuditTrailPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
