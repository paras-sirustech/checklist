<?php

namespace App\Observers;

use App\Models\User;
use App\Models\SupportTicketReply;
use App\Notifications\TicketUpdated;

class SupportTicketReplyObserver
{
    /**
     * Handle the support ticket reply "created" event.
     *
     * @param  \App\Models\SupportTicketReply  $supportTicketReply
     * @return void
     */
    public function created(SupportTicketReply $supportTicketReply)
    {
        $ticket = $supportTicketReply->ticket;
        if ($ticket) {
            $creator = User::find($supportTicketReply->ticket->created_by);
            if ($creator) {
                $creator->notify(new TicketUpdated($ticket, $supportTicketReply));
            }
            if (optional($supportTicketReply->user)->access_level=='Support Staff') {
                $ticket->last_support_team_ticket_update = now();
                $ticket->save();
            }
        }
    }

    /**
     * Handle the support ticket reply "updated" event.
     *
     * @param  \App\Models\SupportTicketReply  $supportTicketReply
     * @return void
     */
    public function updated(SupportTicketReply $supportTicketReply)
    {
        //
    }

    /**
     * Handle the support ticket reply "deleted" event.
     *
     * @param  \App\Models\SupportTicketReply  $supportTicketReply
     * @return void
     */
    public function deleted(SupportTicketReply $supportTicketReply)
    {
        //
    }

    /**
     * Handle the support ticket reply "restored" event.
     *
     * @param  \App\Models\SupportTicketReply  $supportTicketReply
     * @return void
     */
    public function restored(SupportTicketReply $supportTicketReply)
    {
        //
    }

    /**
     * Handle the support ticket reply "force deleted" event.
     *
     * @param  \App\Models\SupportTicketReply  $supportTicketReply
     * @return void
     */
    public function forceDeleted(SupportTicketReply $supportTicketReply)
    {
        //
    }
}
