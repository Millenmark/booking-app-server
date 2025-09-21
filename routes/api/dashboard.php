<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth:sanctum', 'not-customer'])
  ->controller(DashboardController::class)
  ->group(function () {
    Route::post('/booking-analytics', 'getBookingAnalytics');
    Route::post('/revenue-analytics', 'getRevenueAnalytics');
    Route::post('/service-analytics', 'getServiceAnalytics');
  });
