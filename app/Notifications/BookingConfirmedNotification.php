<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Booking $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->booking->ticket->event;
        $totalAmount = $this->booking->total_amount;

        return (new MailMessage)
            ->subject('Booking Confirmed - ' . $event->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been confirmed for the following event:')
            ->line('**Event:** ' . $event->title)
            ->line('**Date:** ' . $event->date->format('M d, Y \a\t g:i A'))
            ->line('**Location:** ' . $event->location)
            ->line('**Ticket Type:** ' . $this->booking->ticket->type)
            ->line('**Quantity:** ' . $this->booking->quantity)
            ->line('**Total Amount:** $' . number_format($totalAmount, 2))
            ->line('**Booking ID:** #' . $this->booking->id)
            ->action('View Event Details', url('/api/events/' . $event->id))
            ->line('Thank you for booking with us!')
            ->line('We look forward to seeing you at the event.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $event = $this->booking->ticket->event;

        return [
            'booking_id' => $this->booking->id,
            'event_title' => $event->title,
            'event_date' => $event->date->format('M d, Y \a\t g:i A'),
            'ticket_type' => $this->booking->ticket->type,
            'quantity' => $this->booking->quantity,
            'total_amount' => $this->booking->total_amount,
            'message' => "Your booking for '{$event->title}' has been confirmed!",
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
