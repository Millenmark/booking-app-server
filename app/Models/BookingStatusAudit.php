<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingStatusAudit extends Model
{
    use HasFactory;

    protected $table = 'booking_status_audits';

    protected $fillable = [
        'booking_id',
        'changed_by',
        'service_name',
        'role',
        'old_status',
        'new_status',
        'changed_at',
        'notes',
    ];

    public $timestamps = false;

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
