<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Get the roles array from user's model
        $userRoles = auth()->user()->roles();

        if (empty(array_intersect($userRoles, $roles))) {
            abort(403, 'Akses ditolak! Anda tidak memiliki izin (Role dibutuhkan: ' . implode(', ', $roles) . ', Role Anda: ' . implode(', ', $userRoles) . ') untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
