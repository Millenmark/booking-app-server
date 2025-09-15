<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'booking_id' => Booking::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'receipt_number' => $this->faker->optional()->numerify('REC-####'),
        ];
    }
}
