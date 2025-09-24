<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $tickets = Ticket::all();

        // Create 20 diverse bookings with realistic scenarios
        $bookings = [
            // Music Festival Bookings
            ['customer_index' => 0, 'ticket_id' => 1, 'quantity' => 2, 'status' => 'confirmed'],
            ['customer_index' => 1, 'ticket_id' => 2, 'quantity' => 1, 'status' => 'confirmed'],
            ['customer_index' => 2, 'ticket_id' => 1, 'quantity' => 4, 'status' => 'confirmed'],
            ['customer_index' => 3, 'ticket_id' => 3, 'quantity' => 1, 'status' => 'pending'],

            // Food & Wine Expo Bookings
            ['customer_index' => 4, 'ticket_id' => 4, 'quantity' => 2, 'status' => 'confirmed'],
            ['customer_index' => 5, 'ticket_id' => 5, 'quantity' => 1, 'status' => 'confirmed'],
            ['customer_index' => 6, 'ticket_id' => 6, 'quantity' => 1, 'status' => 'cancelled'],
            ['customer_index' => 7, 'ticket_id' => 4, 'quantity' => 3, 'status' => 'pending'],

            // Tech Summit Bookings
            ['customer_index' => 8, 'ticket_id' => 7, 'quantity' => 1, 'status' => 'confirmed'],
            ['customer_index' => 9, 'ticket_id' => 8, 'quantity' => 1, 'status' => 'confirmed'],
            ['customer_index' => 0, 'ticket_id' => 9, 'quantity' => 1, 'status' => 'confirmed'],
            ['customer_index' => 1, 'ticket_id' => 7, 'quantity' => 2, 'status' => 'pending'],

            // Basketball Tournament Bookings
            ['customer_index' => 2, 'ticket_id' => 10, 'quantity' => 4, 'status' => 'confirmed'],
            ['customer_index' => 3, 'ticket_id' => 11, 'quantity' => 2, 'status' => 'confirmed'],
            ['customer_index' => 4, 'ticket_id' => 12, 'quantity' => 1, 'status' => 'confirmed'],
            ['customer_index' => 5, 'ticket_id' => 10, 'quantity' => 6, 'status' => 'cancelled'],

            // New Year's Gala Bookings
            ['customer_index' => 6, 'ticket_id' => 13, 'quantity' => 2, 'status' => 'confirmed'],
            ['customer_index' => 7, 'ticket_id' => 14, 'quantity' => 1, 'status' => 'confirmed'],
            ['customer_index' => 8, 'ticket_id' => 15, 'quantity' => 1, 'status' => 'pending'],
            ['customer_index' => 9, 'ticket_id' => 13, 'quantity' => 4, 'status' => 'confirmed'],
        ];

        foreach ($bookings as $bookingData) {
            $customer = $customers[$bookingData['customer_index']];
            $ticket = $tickets->find($bookingData['ticket_id']);

            $booking = Booking::create([
                'user_id' => $customer->id,
                'ticket_id' => $ticket->id,
                'quantity' => $bookingData['quantity'],
                'status' => $bookingData['status'],
            ]);

            // Create payments for confirmed bookings
            if ($bookingData['status'] === 'confirmed') {
                $totalAmount = $ticket->price * $bookingData['quantity'];

                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $totalAmount,
                    'status' => 'success',
                ]);
            }
        }
    }
}
