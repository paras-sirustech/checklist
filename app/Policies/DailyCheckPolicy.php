<?php

namespace App\Policies;

use App\Models\DailyCheck;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DailyCheckPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any daily checks.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the daily check.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DailyCheck  $dailyCheck
     * @return mixed
     */
    public function view(User $user, DailyCheck $dailyCheck)
    {
        return true;
    }

    /**
     * Determine whether the user can create daily checks.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the daily check.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DailyCheck  $dailyCheck
     * @return mixed
     */
    public function update(User $user, DailyCheck $dailyCheck)
    {
        //
    }

    /**
     * Determine whether the user can delete the daily check.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DailyCheck  $dailyCheck
     * @return mixed
     */
    public function delete(User $user, DailyCheck $dailyCheck)
    {
        if ($user->access_level=='Admin') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the daily check.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DailyCheck  $dailyCheck
     * @return mixed
     */
    public function restore(User $user, DailyCheck $dailyCheck)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the daily check.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DailyCheck  $dailyCheck
     * @return mixed
     */
    public function forceDelete(User $user, DailyCheck $dailyCheck)
    {
        return true;
    }
}
