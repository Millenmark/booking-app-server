<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['customer'])) {
            $bookings = Booking::where('customer_id', $user->id)->get();
        } else {
            // staff or admin can see all
            $bookings = Booking::get();
        }

        return response()->json([
            'message'  =>  'Bookings fetched successfully',
            'data' => $bookings
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after:now',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (in_array($user->role, ['customer'])) {
            // Customers can only create for themselves
            if ($validated['customer_id'] != $user->id) {
                abort(403, 'Customers can only create bookings for themselves.');
            }
            $validated['customer_id'] = $user->id;
            $validated['status'] = $validated['status'] ?? 'pending';
        } else {
            // Staff/admin can create for any customer, default status pending if not provided
            $validated['status'] = $validated['status'] ?? 'pending';
        }

        $booking = Booking::create($validated);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::findOrFail($id);
        $user = Auth::user();

        if (in_array($user->role, ['customer']) && $booking->customer_id != $user->id) {
            abort(403, 'You can only view your own bookings.');
        }

        return response()->json([
            'message' => 'Booking fetched successfully',
            'data' => $booking
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);
        $user = Auth::user();

        if (in_array($user->role, ['customer']) && $booking->customer_id != $user->id) {
            abort(403, 'You can only update your own bookings.');
        }

        $oldStatus = $booking->status;

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking->update($validated);

        $newStatus = $booking->fresh()->status;

        if (isset($validated['status']) && $oldStatus !== $newStatus) {
            BookingStatusAudit::create([
                'booking_id' => $booking->id,
                'changed_by' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Booking updated successfully',
            'data' => $booking
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $user = Auth::user();

        if (in_array($user->role, ['customer']) && $booking->customer_id != $user->id) {
            abort(403, 'You can only delete your own bookings.');
        }

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted successfully'
        ], 200);
    }

    /**
     * Process payment for the specified booking.
     */
    public function pay(Booking $booking)
    {
        $user = Auth::user();

        if (in_array($user->role, ['customer']) && $booking->customer_id != $user->id) {
            abort(403, 'You can only pay for your own bookings.');
        }

        if ($booking->status !== 'pending') {
            abort(400, 'Only pending bookings can be paid.');
        }

        $oldStatus = $booking->status;

        $booking->update(['status' => 'confirmed']);

        $newStatus = $booking->status;

        if ($oldStatus !== $newStatus) {
            BookingStatusAudit::create([
                'booking_id' => $booking->id,
                'changed_by' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now(),
                'notes' => 'Status changed via payment confirmation',
            ]);
        }

        return response()->json([
            'message' => 'Booking confirmed via payment successfully',
            'booking' => $booking
        ], 200);
    }
}
