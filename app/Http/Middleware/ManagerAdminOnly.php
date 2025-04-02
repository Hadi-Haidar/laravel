<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'manager') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. Manager role required.'
            ], 403);
        }

        return $next($request);
    }
} 