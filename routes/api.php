<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/')->group(function () {
    require __DIR__ . '/api/auth.php';
});

Route::prefix('bookings')->group(function () {
    require __DIR__ . '/api/bookings.php';
});

Route::prefix('services')->group(function () {
    require __DIR__ . '/api/services.php';
});
