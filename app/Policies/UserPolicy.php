<?php

namespace App\Policies;

use App\Models\User as TargetUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any support tickets.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->access_level=='Admin' || $user->access_level=='Support Staff') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TargetUser  $target_user
     * @return mixed
     */
    public function view(User $user, TargetUser $target_user)
    {
        if ($user->access_level=='Admin' || $user->access_level=='Support Staff') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create support tickets.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->access_level=='Admin') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TargetUser  $target_user
     * @return mixed
     */
    public function update(User $user, TargetUser $target_user)
    {
        if ($user->access_level=='Admin') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupportTicket  $target_user
     * @return mixed
     */
    public function delete(User $user, TargetUser $target_user)
    {
        if ($user->access_level=='Admin') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TargetUser  $target_user
     * @return mixed
     */
    public function restore(User $user, TargetUser $target_user)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TargetUser  $target_user
     * @return mixed
     */
    public function forceDelete(User $user, TargetUser $target_user)
    {
        //
    }
}
