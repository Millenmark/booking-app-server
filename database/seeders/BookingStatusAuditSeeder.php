<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingStatusAudit;
use Illuminate\Database\Seeder;

/**
 * Class BookingStatusAuditSeeder
 * @package Database\Seeders
 */
class BookingStatusAuditSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $bookings = Booking::all();

    foreach ($bookings as $booking) {
      BookingStatusAudit::factory()->create([
        'booking_id' => $booking->id,
        'service_name' => $booking->service->name ?? 'Unknown Service',
        'old_status' => 'pending',
        'new_status' => $booking->status,
      ]);

      // Optionally create more for some bookings
      if (fake()->boolean(30)) { // 30% chance
        BookingStatusAudit::factory()->create([
          'booking_id' => $booking->id,
          'service_name' => $booking->service->name ?? 'Unknown Service',
          'old_status' => $booking->status,
          'new_status' => fake()->randomElement(['confirmed', 'completed', 'cancelled']),
        ]);
      }
    }
  }
}
