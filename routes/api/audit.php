<?php

use App\Http\Controllers\AuditTrailController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'not-customer'])
  ->controller(AuditTrailController::class)
  ->group(function () {
    Route::get('/booking-status', 'getBookingStatusAudit');
  });
