<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdated extends Notification
{
    use Queueable;

    protected $support_ticket;
    protected $ticket_reply;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($support_ticket, $ticket_reply)
    {
        $this->support_ticket = $support_ticket;
        $this->ticket_reply = $ticket_reply;
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
                    ->subject('Ticket #' . $this->support_ticket->id . ' has been updated - ' . config('app.name'))
                    ->greeting('Ticket Updated!')
                    //->cc($this->support_ticket->email_cc())
                    ->line('----------------------------------------------')
                    ->line($this->ticket_reply->description)
                    ->line('----------------------------------------------')
                    ->line('Updated By: ' . $this->ticket_reply->user->name)
                    ->line('Updated On: ' . formattedDateTimeString($this->ticket_reply->created_at))
                    ->line('Ticket Subject: ' . $this->support_ticket->subject)
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
