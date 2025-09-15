<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();

        $confirmedBookings = Booking::where('customer_id', $customer->id)
            ->where('status', 'confirmed')
            ->get();

        foreach ($confirmedBookings as $booking) {
            Payment::create([
                'customer_id' => $booking->customer_id,
                'booking_id' => $booking->id,
                'amount' => fake()->randomFloat(2, 10, 1000),
                'paid_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'receipt_number' => fake()->optional()->numerify('REC-####'),
            ]);
        }
    }
}
