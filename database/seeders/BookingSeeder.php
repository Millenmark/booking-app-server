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
    $customers = User::where('role', 'customer')->get();

    foreach ($customers as $customer) {
      Booking::factory(5)->for($customer, 'customer')->create();
      Booking::factory(2)->for($customer, 'customer')->create([
        'status' => 'confirmed'
      ]);
    }
  }
}
