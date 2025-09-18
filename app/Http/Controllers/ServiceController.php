<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function getAllServices(): JsonResponse
    {
        return response()->json([
            'message' => 'Services fetched successfully',
            'data' => Service::get()
        ]);
    }
}
