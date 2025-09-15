<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/', [BookingController::class, 'index']);
  Route::post('/', [BookingController::class, 'store']);
  Route::get('/{booking}', [BookingController::class, 'show']);
  Route::put('/{booking}', [BookingController::class, 'update']);
  Route::delete('/{booking}', [BookingController::class, 'destroy']);
  Route::post('/{booking}/pay', [BookingController::class, 'pay']);
});
