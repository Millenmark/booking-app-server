<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->role !== 'customer' || $booking->customer_id === $user->id;
    }

    public function scopeViewAny(User $user, $query)
    {
        if ($user->role === 'customer') {
            return $query->where('bookings.customer_id', $user->id)
                ->where('bookings.status', 'pending')
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->select(
                    'bookings.id',
                    'bookings.scheduled_at as schedule',
                    'services.name as name',
                    'services.price as price'
                );
        }

        // Admin sees all with relations
        return $query->select('id', 'service_id', 'scheduled_at')
            ->with('service', 'payment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->role !== 'customer' || $booking->customer_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->role !== 'customer' || $booking->customer_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}
