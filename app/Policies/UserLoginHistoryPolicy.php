<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserLoginHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserLoginHistoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->access_level=='Admin') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserLoginHistory  $user_login_history
     * @return mixed
     */
    public function view(User $user, UserLoginHistory $user_login_history)
    {
        return true;
    }
}
