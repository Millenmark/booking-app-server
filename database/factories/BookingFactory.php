<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
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
    $services = [
      "Haircut",
      "Hair Coloring",
      "Manicure",
      "Pedicure",
      "Massage Therapy",
      "Facial Treatment",
      "Waxing"
    ];

    return [
      'customer_id' => User::factory(),
      'service_name' => fake()->randomElement($services),
      'scheduled_at' => fake()->dateTimeBetween(now(), now()->addYear()),
      'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
      'notes' => fake()->optional()->text(),
    ];
  }
}
