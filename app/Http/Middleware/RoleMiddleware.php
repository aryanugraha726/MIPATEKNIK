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

        // Get the role string from user's role relation
        $userRole = auth()->user()->role->nama_role ?? '';

        if (!in_array($userRole, $roles)) {
            abort(403, 'Akses ditolak! Anda tidak memiliki izin (Role: ' . implode(', ', $roles) . ') untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
