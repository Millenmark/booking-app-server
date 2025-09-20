<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingStatusAudit;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingStatusAudit>
 */
class BookingStatusAuditFactory extends Factory
{
  private static $counter = 0;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {

    return [
      'booking_id' => Booking::factory(),
      'changed_by' => fake()->name(),
      'service_name' => function (array $attributes) {
        $booking = Booking::find($attributes['booking_id']);
        return $booking?->service?->name;
      },
      'role' => fake()->randomElement(['admin', 'staff', 'customer']),
      'old_status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
      'new_status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
      'changed_at' => now()->addSeconds(self::$counter++),
      'notes' => fake()->optional()->sentence(),
    ];
  }
}
