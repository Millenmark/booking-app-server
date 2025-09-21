<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
  ->controller(BookingController::class)
  ->group(function () {
    Route::get('/', 'getAllBookings');
    Route::post('/', 'createBooking');
    Route::get('/{booking}', 'getSingleBooking');
    Route::put('/{booking}', 'updateBooking');
    Route::delete('/{booking}', 'deleteBooking');
  });
