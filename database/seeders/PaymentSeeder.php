<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $confirmedBookings = Booking::where('status', 'confirmed')->with('service')->get();

        foreach ($confirmedBookings as $booking) {
            Payment::create([
                'customer_id' => $booking->customer_id,
                'booking_id' => $booking->id,
                'amount' => $booking->service->price,
                'paid_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'receipt_number' => fake()->numerify('REC-####'),
            ]);
        }
    }
}
