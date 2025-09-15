<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\BookingSeeder;
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
            'email' => 'test@example.com',
        ]);

        $this->call(BookingSeeder::class);
    }
}
