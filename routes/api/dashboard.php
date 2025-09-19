<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth:sanctum', 'not-customer'])->group(function () {
  Route::post('/booking-analytics', [DashboardController::class, 'getBookingAnalytics']);
  Route::post('/revenue-analytics', [DashboardController::class, 'getRevenueAnalytics']);
});
