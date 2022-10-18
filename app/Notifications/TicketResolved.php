<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolved extends Notification
{
    use Queueable;

    protected $support_ticket;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($support_ticket)
    {
        $this->support_ticket = $support_ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ticket #' . $this->support_ticket->id . ' resolved - ' . config('app.name'))
            ->line('A ticket has been resolved by support staff.')
            ->line('Ticket Subject: ' . $this->support_ticket->subject)
            ->line('Shop: ' . optional($this->support_ticket->shop)->name)
            ->line('Description: ' . $this->support_ticket->description)
            ->line('Created By: ' . optional($this->support_ticket->creator)->name)
            ->line('Assigned To: ' . optional($this->support_ticket->assignee)->name)
            ->action('View the Ticket', $this->support_ticket->url)
            ->line('Thank you for using Checklist Application')
            ->attach(public_path('/storage/' . $this->support_ticket->attachment), ['as' => 'campaign', 'mime' => 'image/png']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
