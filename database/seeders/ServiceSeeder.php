<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
  public function run(): void
  {
    $services = [
      [
        'name' => 'Haircut',
        'description' => 'Standard haircut service.',
        'price' => 25.00,
        'duration_minutes' => 30,
        'is_active' => true,
      ],
      [
        'name' => 'Hair Coloring',
        'description' => 'Full hair coloring treatment.',
        'price' => 80.00,
        'duration_minutes' => 120,
        'is_active' => true,
      ],
      [
        'name' => 'Manicure',
        'description' => 'Basic manicure service.',
        'price' => 20.00,
        'duration_minutes' => 45,
        'is_active' => true,
      ],
      [
        'name' => 'Pedicure',
        'description' => 'Basic pedicure service.',
        'price' => 25.00,
        'duration_minutes' => 60,
        'is_active' => true,
      ],
      [
        'name' => 'Massage Therapy',
        'description' => 'Relaxing massage session.',
        'price' => 60.00,
        'duration_minutes' => 60,
        'is_active' => true,
      ],
      [
        'name' => 'Facial Treatment',
        'description' => 'Cleansing facial treatment.',
        'price' => 50.00,
        'duration_minutes' => 45,
        'is_active' => true,
      ],
      [
        'name' => 'Waxing',
        'description' => 'Full body waxing service.',
        'price' => 40.00,
        'duration_minutes' => 30,
        'is_active' => true,
      ],
    ];

    foreach ($services as $service) {
      Service::create($service);
    }
  }
}
