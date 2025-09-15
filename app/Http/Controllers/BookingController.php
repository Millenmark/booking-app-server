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
        $bookings = Booking::get();
        return response()->json([
            'message' =>  'Bookings fetched successfully',
            'data' => $bookings
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        $newStatus = $booking->fresh()->status;

        if ($oldStatus !== $newStatus) {
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
        //
    }

    /**
     * Process payment for the specified booking.
     */
    public function pay(Booking $booking)
    {
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
