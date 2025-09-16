<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusAudit;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['customer'])) {
            $bookings = Booking::where('customer_id', $user->id)->get();
        } else {
            $bookings = Booking::with('payment')->get();
        }

        return response()->json([
            'message'  =>  'Bookings fetched successfully',
            'data' => $bookings
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (in_array($user->role, ['customer'])) {
            $validated['customer_id'] = $user->id;
            $validated['status'] = 'pending';
        } else {
            abort(403, 'Only Customers can create booking');
        }

        $booking = Booking::create($validated);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    public function show(Booking $booking)
    {
        $booking->load('payment');

        $user = Auth::user();

        if ($user->role === 'customer' && $booking->customer_id != $user->id) {
            abort(403, 'You can only view your own bookings.');
        }

        return response()->json([
            'message' => 'Booking fetched successfully',
            'data' => $booking
        ]);
    }


    public function update(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $payment = null;

        if ($user->role === 'customer') {
            if ($booking->customer_id != $user->id) {
                abort(403, 'You can only update your own bookings.');
            }

            $validated = $request->validate([
                'status' => 'required|in:cancelled',
            ]);

            $oldStatus = $booking->status;

            $booking->update([
                'status' => 'cancelled',
            ]);

            BookingStatusAudit::create([
                'booking_id' => $booking->id,
                'changed_by' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
                'changed_at' => now(),
            ]);
        } else {
            $oldStatus = $booking->status;

            $validated = $request->validate([
                'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
                'notes' => 'nullable|string|max:1000',
            ]);

            $booking->update($validated);
            $newStatus = $booking->fresh()->status;

            if (($validated['status'] ?? null) === 'confirmed') {
                $payment = Payment::create([
                    'customer_id' => $booking->customer_id,
                    'booking_id' => $booking->id,
                    'amount' => $booking->service->price ?? 0,
                    'paid_at' => now(),
                    'receipt_number' => 'REC-' . strtoupper(Str::random(8)) . $booking->id . $booking->customer_id,
                ]);
            }

            if (isset($validated['status']) && $oldStatus !== $newStatus) {
                BookingStatusAudit::create([
                    'booking_id' => $booking->id,
                    'changed_by' => $user->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'changed_at' => now(),
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
        }

        $response = [
            'message' => 'Booking updated successfully',
            'data' => $booking->fresh(),
        ];

        if ($payment) {
            $response['data']['receipt'] = $payment;
        }

        return response()->json($response);
    }



    public function destroy(Booking $booking)
    {
        $user = Auth::user();

        if (in_array($user->role, ['customer']) && $booking->customer_id != $user->id) {
            abort(403, 'You can only delete your own bookings.');
        }

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted successfully'
        ]);
    }
}
