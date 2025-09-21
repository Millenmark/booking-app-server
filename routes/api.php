<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['checkApiKey'])->group(function () {

    Route::prefix('/')->group(function () {
        require __DIR__ . '/api/auth.php';
    });

    Route::prefix('bookings')->group(function () {
        require __DIR__ . '/api/bookings.php';
    });

    Route::prefix('services')->group(function () {
        require __DIR__ . '/api/services.php';
    });

    Route::prefix('dashboard')->group(function () {
        require __DIR__ . '/api/dashboard.php';
    });

    Route::prefix('audit')->group(function () {
        require __DIR__ . '/api/audit.php';
    });
});
