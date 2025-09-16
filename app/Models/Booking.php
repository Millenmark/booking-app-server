<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    /** @use HasFactory */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'service_id',
        'scheduled_at',
        'status',
        'notes',
    ];

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service for the booking.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the status audits for the booking.
     */
    public function statusAudits()
    {
        return $this->hasMany(BookingStatusAudit::class);
    }

    /**
     * Get the payment for the booking.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
