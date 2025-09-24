<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'date' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'location' => fake()->city() . ', ' . fake()->state(),
            'created_by' => User::factory()->organizer(),
        ];
    }

    public function upcoming()
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeBetween('+1 day', '+3 months'),
        ]);
    }

    public function past()
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeBetween('-6 months', '-1 day'),
        ]);
    }
}
