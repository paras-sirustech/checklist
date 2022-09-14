<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketEscalation extends Notification
{
    use Queueable;

    protected $support_ticket;
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($support_ticket, $type)
    {
        $this->support_ticket = $support_ticket;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->type . ' Ticket has been escalated to you - ' . config('app.name'))
                    ->line('Following ticket has been escalated to you:')
                    ->line('Shop: ' . optional($this->support_ticket->shop)->name)
                    ->line('Ticket Subject: ' . $this->support_ticket->subject)
                    ->line('Description: ' . $this->support_ticket->description)
                    ->line('Created By: ' . optional($this->support_ticket->creator)->name)
                    ->line('Assigned To: ' . optional($this->support_ticket->assignee)->name)
                    ->line('Last Updated: ' . formattedDateTimeString($this->support_ticket->last_support_team_ticket_update))
                    //->line('Created On: ' . $this->support_ticket->created_at->toDayDateTimeString())
                    ->action('View the Ticket', $this->support_ticket->url)
                    ->line('Thank you for using Checklist Application');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
