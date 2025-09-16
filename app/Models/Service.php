<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'description',
    'price',
    'duration_minutes',
    'is_active',
  ];

  /**
   * The attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'price' => 'decimal:2',
      'is_active' => 'boolean',
    ];
  }

  /**
   * Get the bookings for the service.
   */
  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }
}
