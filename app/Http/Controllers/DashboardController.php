<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getBookingAnalytics(Request $request): JsonResponse
    {
        $baseQuery = Booking::query();

        $total = (clone $baseQuery)->withDateFilter($request)->count();
        $unpaid = (clone $baseQuery)->withDateFilter($request)->where('status', 'pending')->count();
        $paid = (clone $baseQuery)->withDateFilter($request)->has('payment')->count();
        $conversion_rate = $total > 0 ? round(($paid / $total) * 100, 2) : 0;

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $daysInMonth = $end->daysInMonth;

        $dailyTotals = (clone $baseQuery)
            ->selectRaw('DATE(scheduled_at) as day, COUNT(*) as total')
            ->whereBetween('scheduled_at', [$start, $end])
            ->groupBy(DB::raw('DATE(scheduled_at)'))
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $dailyData = array_fill(0, $daysInMonth, 0);
        foreach ($dailyTotals as $date => $count) {
            $day = Carbon::parse($date)->day;
            $dailyData[$day - 1] = (int) $count;
        }

        return response()->json([
            'total'           => $total,
            'unpaid'          => $unpaid,
            'paid'            => $paid,
            'conversion_rate' => $conversion_rate,
            'daily'           => $dailyData,
        ]);
    }


    public function getRevenueAnalytics(Request $request): JsonResponse
    {
        $baseQuery = Payment::query();

        $total = (clone $baseQuery)->withDateFilter($request, 'paid_at')->sum('amount');

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $daysInMonth = $end->daysInMonth;

        $dailyTotals = (clone $baseQuery)
            ->selectRaw('DATE(paid_at) as day, SUM(amount) as total')
            ->whereBetween('paid_at', [$start, $end])
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $dailyData = array_fill(0, $daysInMonth, 0.0);
        foreach ($dailyTotals as $date => $sum) {
            $day = Carbon::parse($date)->day;
            $dailyData[$day - 1] = (float) $sum;
        }

        return response()->json([
            'total' => (float) $total,
            'daily' => $dailyData,
        ]);
    }

    public function getServiceAnalytics(Request $request): JsonResponse
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $baseQuery = Booking::whereBetween('scheduled_at', [$start, $end]);

        $bookingCounts = $baseQuery
            ->select('service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('service_id')
            ->pluck('total', 'service_id')
            ->toArray();

        $allServices = Service::pluck('name', 'id')->toArray();

        $serviceCounts = [];
        foreach ($allServices as $id => $name) {
            $serviceCounts[$name] = isset($bookingCounts[$id]) ? (int) $bookingCounts[$id] : 0;
        }

        ksort($serviceCounts);

        return response()->json([
            'top_services' => $serviceCounts,
        ]);
    }
}
