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
    $serviceNames = [
      "Haircut",
      "Hair Coloring",
      "Manicure",
      "Pedicure",
      "Massage Therapy",
      "Facial Treatment",
      "Waxing"
    ];

    $service = Service::create([
      'name' => fake()->randomElement($serviceNames),
      'description' => fake()->text(),
      'price' => fake()->randomFloat(2, 10, 200),
      'duration_minutes' => fake()->numberBetween(15, 120),
      'is_active' => true,
    ]);

    return [
      'customer_id' => User::factory(),
      'service_id' => $service->id,
      'scheduled_at' => fake()->dateTimeBetween(now(), now()->addYear()),
      'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
      'notes' => fake()->optional()->text(),
    ];
  }
}
