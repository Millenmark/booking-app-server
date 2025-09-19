<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusAudit;
use App\Models\Payment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function getAllBookings(): JsonResponse
    {
        $user = Auth::user();

        if (in_array($user->role, ['customer'])) {
            $data = Booking::where('customer_id', $user->id)
                ->where('status', 'pending')
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->select(
                    'bookings.id',
                    'bookings.scheduled_at as schedule',
                    'services.name as name',
                    'services.price as price'
                )
                ->get();
        } else {
            $data = Booking::select('id', 'service_id', 'scheduled_at')
                ->with('service')
                ->with('payment')
                ->get();
        }

        return response()->json([
            'message' => 'Bookings fetched successfully',
            'data'    => $data
        ]);
    }


    public function createBooking(Request $request): JsonResponse
    {

        $user = Auth::user();

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['customer_id'] = $user->id;
        $validated['status'] = 'pending';

        $service = Service::findOrFail($validated['service_id']);
        $duration = $service->duration_minutes;
        $newStart = Carbon::parse($validated['scheduled_at']);
        $newEnd = $newStart->copy()->addMinutes($duration);

        $overlappingBookings = Booking::where('customer_id', $validated['customer_id'])
            ->where('status', '!=', 'cancelled')
            ->with('service')
            ->get();

        foreach ($overlappingBookings as $existing) {
            $existingStart = Carbon::parse($existing->scheduled_at);
            $existingService = $existing->service;
            $existingEnd = $existingStart->copy()->addMinutes($existingService->duration_minutes);

            if ($existingStart->lt($newEnd) && $existingEnd->gt($newStart)) {
                return response()->json([
                    'message' => 'Booking overlaps with an existing booking.',
                    'errors' => [
                        'scheduled_at' => ['Overlaps with existing booking at ' . $existing->scheduled_at]
                    ]
                ], 422);
            }
        }

        $booking = Booking::create($validated);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    public function getSingleBooking(Booking $booking): JsonResponse
    {

        $booking->load('payment');

        return response()->json([
            'message' => 'Booking fetched successfully',
            'data' => $booking
        ]);
    }


    public function updateBooking(Request $request, Booking $booking): JsonResponse
    {

        $user = Auth::user();
        $payment = null;

        if ($user->role === 'customer') {
            $validated = $request->validate([
                'status' => 'required|in:cancelled',
            ]);

            $oldStatus = $booking->status;

            $booking->update([
                'status' => 'cancelled',
            ]);

            BookingStatusAudit::create([
                'booking_id' => $booking->id,
                'service_name' => $booking->service->name,
                'changed_by' => $user->name,
                'role' => $user->role,
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
                    'service_name' => $booking->service->name,
                    'changed_by' => $user->name,
                    'role' => $user->role,
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



    public function deleteBooking(Booking $booking): JsonResponse
    {

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted successfully'
        ]);
    }
}
