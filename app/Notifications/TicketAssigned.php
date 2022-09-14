<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssigned extends Notification
{
    use Queueable;

    protected $support_ticket;
    protected $shop;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($support_ticket, $shop)
    {
        $this->support_ticket = $support_ticket;
        $this->shop = $shop;
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
                    //->cc($this->support_ticket->email_cc())
                    ->subject('Ticket has been created - ' . config('app.name'))
                    ->line('A new ticket has been created.')
                    ->line('Created By: ' . $this->support_ticket->creator->name)
                    ->line('Assigned To: ' . optional($this->support_ticket->assignee)->name)
                    ->line('Shop: ' . optional($this->shop)->name)
                    ->line('Ticket Subject: ' . $this->support_ticket->subject)
                    ->line('Description: ' . $this->support_ticket->description)
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
