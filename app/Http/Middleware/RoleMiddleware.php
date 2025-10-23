<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // The middleware receives a single role string (e.g., 'encoder').
        // If you want to allow multiple roles on a single route (e.g., 'encoder,admin'),
        // you could split $role by ',' and check in_array(Auth::user()->role, $allowedRoles).
        // For now we enforce strict equality to keep behavior explicit.
        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}