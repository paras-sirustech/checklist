<?php

namespace App\Observers;

use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ChecklistItem;
use Notification;
use App\Notifications\TicketAssigned;
use Illuminate\Support\Facades\DB;

class SupportTicketObserver
{
    /**
     * Handle the support ticket reply "created" event.
     *
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return void
     */
    public function created(SupportTicket $supportTicket)
    {
        $supportTicket_id = $supportTicket->id;
        if ($supportTicket->subject!='') {
            // Search checklist item
            $checklist_item = ChecklistItem::where('name', $supportTicket->subject)->first();
            if ($checklist_item) {
                DB::table('support_tickets')->where('id', $supportTicket->id)->update(['checklist_item_id' => $checklist_item->id]);
            }
        }

        if (optional($supportTicket->creator)->email!='') {
            $to_email[] = $supportTicket->creator->email;
        }

        if (optional($supportTicket->assignee)->email!='') {
            $to_email[] = $supportTicket->assignee->email;
        }

        $to_email[] = config('services.l2escalation_email');

        if ($supportTicket->shop) {
            // Branch Manager
            $shop_manager_email = optional($supportTicket->shop->branch_managers->first())->email;
            if ($shop_manager_email!='') {
                $to_email[] = $shop_manager_email;
            }

            // If this is a Franchisee shop
            if ($supportTicket->shop->type=='Franchisee') {
                $to_email[] = config('services.franchisee_manager');
            }
        }

        if (count($to_email)>0) {
            Notification::route('mail', $to_email)->notify(new TicketAssigned($supportTicket, $supportTicket->shop));
            $log = 'New Ticket created: ' . $supportTicket_id . '. Notified to: ' . json_encode($to_email, false);
            info($log);
        }
    }

    /**
     * Handle the support ticket reply "updated" event.
     *
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return void
     */
    public function updated(SupportTicket $supportTicket)
    {
        //
    }

    /**
     * Handle the support ticket reply "deleted" event.
     *
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return void
     */
    public function deleted(SupportTicket $supportTicket)
    {
        //
    }

    /**
     * Handle the support ticket reply "restored" event.
     *
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return void
     */
    public function restored(SupportTicket $supportTicket)
    {
        //
    }

    /**
     * Handle the support ticket reply "force deleted" event.
     *
     * @param  \App\Models\SupportTicket  $supportTicket
     * @return void
     */
    public function forceDeleted(SupportTicket $supportTicket)
    {
        //
    }
}
