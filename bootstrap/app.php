<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                // $structuredData = [];
                // if (str_contains($request->route()->uri() ?? '', 'bookings')) {
                //     $structuredData = ['type' => 'booking', 'suggestion' => 'Check the booking ID'];
                // } elseif (str_contains($request->route()->uri() ?? '', 'auth')) {
                //     $structuredData = ['type' => 'auth', 'suggestion' => 'Verify credentials'];
                // }

                return response()->json([
                    'message' => 'Resource not found',
                    // 'data' => $structuredData,
                    'timestamp' => now()->toISOString()
                ], 404);
            }
        });

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    // 'data' => [
                    //     'errors' => $e->errors(),
                    //     'fields' => array_keys($e->errors())
                    // ],
                    'timestamp' => now()->toISOString()
                ], 422);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'timestamp' => now()->toISOString()
                ], 401);
            }
        });

        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                Log::error('API Error in ' . $request->path() . ': ' . $e->getMessage(), [
                    'exception' => $e,
                    'user_id' => Auth::id() ?? null,
                    'request_data' => $request->except(['password'])
                ]);

                return response()->json([
                    'message' => $e->getMessage() ?: 'An internal server error occurred',
                    'timestamp' => now()->toISOString()
                ], 500);
            }
        });
    })->create();
