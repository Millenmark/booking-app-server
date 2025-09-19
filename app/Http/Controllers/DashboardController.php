<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getTotalBookings(Request $request)
    {

        $query = Booking::query();

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay(),
            ]);
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        $total = $query->count();

        return response()->json([
            'total' => $total
        ]);
    }
}
