<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function suspendUser(User $user) {
        if ( ! $user->is_root ) {
            return false;
        }

        return true;
    }

    public function isRootUser(User $user) {
        if ( ! $user->is_root ) {
            return false;
        }

        return true;
    }
    
}
