<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizers = User::where('role', 'organizer')->get();

        $events = [
            [
                'title' => 'Summer Music Festival 2025',
                'description' => 'Join us for the biggest music festival of the year featuring over 50 artists across multiple genres. Food trucks, craft beer, and unforgettable performances await!',
                'date' => Carbon::create(2025, 7, 15, 18, 0, 0),
                'location' => 'Central Park, New York, NY',
                'created_by' => $organizers[0]->id, // Music Events Co.
            ],
            [
                'title' => 'International Food & Wine Expo',
                'description' => 'Experience culinary delights from around the world. Meet renowned chefs, taste exotic wines, and learn cooking techniques from the masters.',
                'date' => Carbon::create(2025, 9, 20, 11, 0, 0),
                'location' => 'Convention Center, Los Angeles, CA',
                'created_by' => $organizers[2]->id, // Cultural Events Inc.
            ],
            [
                'title' => 'Tech Innovation Summit 2025',
                'description' => 'Discover the latest in technology innovation. Network with industry leaders, attend keynote speeches, and explore cutting-edge tech demos.',
                'date' => Carbon::create(2025, 10, 5, 9, 0, 0),
                'location' => 'Moscone Center, San Francisco, CA',
                'created_by' => $organizers[2]->id, // Cultural Events Inc.
            ],
            [
                'title' => 'Championship Basketball Tournament',
                'description' => 'Watch the best college basketball teams compete for the championship title. High-energy games, passionate fans, and championship glory!',
                'date' => Carbon::create(2025, 11, 12, 19, 30, 0),
                'location' => 'Madison Square Garden, New York, NY',
                'created_by' => $organizers[1]->id, // Sports Events LLC
            ],
            [
                'title' => 'New Year\'s Eve Gala 2025',
                'description' => 'Ring in the New Year with style! Elegant dinner, live entertainment, champagne toast at midnight, and dancing until dawn.',
                'date' => Carbon::create(2025, 12, 31, 20, 0, 0),
                'location' => 'Grand Ballroom, Chicago, IL',
                'created_by' => $organizers[0]->id, // Music Events Co.
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }
    }
}
