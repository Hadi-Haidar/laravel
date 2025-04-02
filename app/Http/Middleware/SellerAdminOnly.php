<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'seller') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. Seller role required.'
            ], 403);
        }

        return $next($request);
    }
} 