<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth:sanctum', 'not-customer'])->group(function () {
  Route::post('/total-bookings', [DashboardController::class, 'getTotalBookings']);
});
