<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Check if the user's role is inside the allowed list
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // If unauthorized, redirect back or to home with error
        return redirect('/home')->with('error', 'You do not have permission to access this page.');
    }
}