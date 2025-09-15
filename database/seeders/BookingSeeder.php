<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class BookingSeeder
 * @package Database\Seeders
 */
class BookingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = User::where('role', 'customer')->first();

    Booking::factory(5)->for($user, 'customer')->create();
  }
}
