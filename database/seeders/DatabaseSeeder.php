<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            EventSeeder::class,
            TicketSeeder::class,
            BookingSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Seeded data summary:');
        $this->command->info('- 2 Admins');
        $this->command->info('- 3 Organizers');
        $this->command->info('- 10 Customers');
        $this->command->info('- 5 Events');
        $this->command->info('- 15 Tickets (3 per event)');
        $this->command->info('- 20 Bookings (various statuses)');
        $this->command->info('- Payments created for confirmed bookings');
    }
}
