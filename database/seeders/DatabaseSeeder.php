<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\BookingSeeder;
use Database\Seeders\PaymentSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
            'email' => 'admin@mailsac.com',
        ]);

        User::factory(1)->create([
            'role' => 'staff',
            'email' => 'staff@mailsac.com'
        ]);

        User::factory(2)
            ->create(new Sequence(
                fn($sequence) => [
                    'role' => 'customer',
                    'email' => 'customer' . ($sequence->index + 1) . '@mailsac.com'
                ]
            ));

        $this->call(ServiceSeeder::class);
        $this->call(BookingSeeder::class);
        $this->call(PaymentSeeder::class);
    }
}
