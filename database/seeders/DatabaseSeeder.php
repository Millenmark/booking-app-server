<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\BookingSeeder;
use Database\Seeders\PaymentSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Aki',
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        User::factory(1)->create([
            'role' => 'staff',
            'email' => 'staff@example.com'
        ]);

        User::factory(1)->create([
            'role' => 'customer',
            'email' => 'customer@example.com'
        ]);

        $this->call(BookingSeeder::class);
        $this->call(PaymentSeeder::class);
    }
}
