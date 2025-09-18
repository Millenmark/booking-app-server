<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/', [BookingController::class, 'getAllBookings']);
  Route::post('/', [BookingController::class, 'createBooking']);
  Route::get('/{booking}', [BookingController::class, 'getSingleBooking']);
  Route::put('/{booking}', [BookingController::class, 'updateBooking']);
  Route::delete('/{booking}', [BookingController::class, 'deleteBooking']);
});
