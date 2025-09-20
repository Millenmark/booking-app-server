<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BookingStatusAudit;

class AuditTrailController extends Controller
{
    public function getBookingStatusAudit(): JsonResponse
    {
        return response()->json([
            'message' => 'Booking Status Audit fetched successfully',
            'data' => BookingStatusAudit::with('booking')->get()
        ]);
    }
}
