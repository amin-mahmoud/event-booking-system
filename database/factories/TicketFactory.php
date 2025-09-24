<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['General Admission', 'VIP', 'Premium', 'Early Bird']),
            'price' => fake()->randomFloat(2, 10, 500),
            'quantity' => fake()->numberBetween(10, 1000),
            'event_id' => Event::factory(),
        ];
    }

    public function vip()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'VIP',
            'price' => fake()->randomFloat(2, 200, 1000),
        ]);
    }
}
