<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Expect roles as comma-separated list in middleware parameter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $user = auth()->user();
        if (! $user) {
            abort(403, 'Forbidden');
        }

        $roleName = $user->role ? $user->role->name : null;
        if (! $roles) {
            return $next($request);
        }

        $allowed = array_map('trim', explode(',', $roles));
        if (! in_array($roleName, $allowed)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
