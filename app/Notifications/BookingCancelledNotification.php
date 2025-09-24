<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->booking->ticket->event;

        return (new MailMessage)
            ->subject('Booking Cancelled - ' . $event->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been cancelled for:')
            ->line('**Event:** ' . $event->title)
            ->line('**Booking ID:** #' . $this->booking->id)
            ->line('If you have any questions, please contact our support team.')
            ->line('Thank you for your understanding.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'event_title' => $this->booking->ticket->event->title,
            'message' => "Your booking for '{$this->booking->ticket->event->title}' has been cancelled.",
        ];
    }
}
