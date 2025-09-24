<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        $ticketData = [
            // Summer Music Festival (Event 1)
            [
                ['event_id' => 1, 'type' => 'General Admission', 'price' => 89.99, 'quantity' => 5000],
                ['event_id' => 1, 'type' => 'VIP Experience', 'price' => 299.99, 'quantity' => 500],
                ['event_id' => 1, 'type' => 'Backstage Pass', 'price' => 599.99, 'quantity' => 100],
            ],
            // Food & Wine Expo (Event 2)
            [
                ['event_id' => 2, 'type' => 'Standard Entry', 'price' => 45.00, 'quantity' => 2000],
                ['event_id' => 2, 'type' => 'Premium Tasting', 'price' => 125.00, 'quantity' => 800],
                ['event_id' => 2, 'type' => 'Chef\'s Table', 'price' => 350.00, 'quantity' => 50],
            ],
            // Tech Summit (Event 3)
            [
                ['event_id' => 3, 'type' => 'Conference Pass', 'price' => 199.99, 'quantity' => 1500],
                ['event_id' => 3, 'type' => 'Workshop Access', 'price' => 449.99, 'quantity' => 300],
                ['event_id' => 3, 'type' => 'Networking Plus', 'price' => 699.99, 'quantity' => 100],
            ],
            // Basketball Tournament (Event 4)
            [
                ['event_id' => 4, 'type' => 'Upper Bowl', 'price' => 35.00, 'quantity' => 8000],
                ['event_id' => 4, 'type' => 'Lower Bowl', 'price' => 85.00, 'quantity' => 4000],
                ['event_id' => 4, 'type' => 'Courtside', 'price' => 500.00, 'quantity' => 100],
            ],
            // New Year's Gala (Event 5)
            [
                ['event_id' => 5, 'type' => 'Standard Dinner', 'price' => 150.00, 'quantity' => 800],
                ['event_id' => 5, 'type' => 'Premium Package', 'price' => 350.00, 'quantity' => 200],
                ['event_id' => 5, 'type' => 'Champagne Suite', 'price' => 750.00, 'quantity' => 50],
            ],
        ];

        // Create exactly 15 tickets (3 per event)
        foreach ($ticketData as $eventTickets) {
            foreach ($eventTickets as $ticket) {
                Ticket::create($ticket);
            }
        }
    }
}
