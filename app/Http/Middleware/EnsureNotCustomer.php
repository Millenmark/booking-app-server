<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureNotCustomer
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->role === 'customer') {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
