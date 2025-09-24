<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'status' => fake()->randomElement(['success', 'failed', 'refunded']),
        ];
    }

    public function successful()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
        ]);
    }
}
