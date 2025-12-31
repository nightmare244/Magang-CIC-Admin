<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user login dulu
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Cek role
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return $next($request);
    }
}
