<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getBookingAnalytics(Request $request)
    {
        $baseQuery = Booking::query()->withDateFilter($request);

        return response()->json([
            'total' => (clone $baseQuery)->count(),
            'unpaid' => (clone $baseQuery)->where('status', 'pending')->count(),
        ]);
    }

    public function getRevenueAnalytics(Request $request)
    {

        $total = Payment::withDateFilter($request)
            ->sum('amount');

        return response()->json([
            'total' => (float) $total
        ]);
    }
}
