<?php

namespace App\Policies;

use App\Models\ShopIpAddress;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShopIpAddressPolicy
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
        if ($user->access_level=='Admin') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShopIpAddress  $ip_address
     * @return mixed
     */
    public function view(User $user, ShopIpAddress $ip_address)
    {
        return true;
    }

    /**
     * Determine whether the user can create support tickets.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShopIpAddress  $ip_address
     * @return mixed
     */
    public function update(User $user, ShopIpAddress $ip_address)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupportTicket  $ip_address
     * @return mixed
     */
    public function delete(User $user, ShopIpAddress $ip_address)
    {
        //
    }

    /**
     * Determine whether the user can restore the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShopIpAddress  $ip_address
     * @return mixed
     */
    public function restore(User $user, ShopIpAddress $ip_address)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShopIpAddress  $ip_address
     * @return mixed
     */
    public function forceDelete(User $user, ShopIpAddress $ip_address)
    {
        //
    }
}
