<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupportTicketPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return mixed
     */
    public function view(User $user, SupportTicket $supportTicket)
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
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return mixed
     */
    public function update(User $user, SupportTicket $supportTicket)
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
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return mixed
     */
    public function delete(User $user, SupportTicket $supportTicket)
    {
        //
    }

    /**
     * Determine whether the user can restore the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return mixed
     */
    public function restore(User $user, SupportTicket $supportTicket)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the support ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return mixed
     */
    public function forceDelete(User $user, SupportTicket $supportTicket)
    {
        //
    }
}
