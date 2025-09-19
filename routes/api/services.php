<?php

use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::controller(ServiceController::class)->group(function () {
  Route::get('services', 'getAllServices');
});
