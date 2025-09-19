<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {

    return [
      'customer_id' => User::factory(),
      'service_id' => Service::inRandomOrder()->first()->id,
      'scheduled_at' => fake()->dateTimeBetween(now(), now()->addYear()),
      'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
      'notes' => fake()->optional()->text(),
    ];
  }
}
