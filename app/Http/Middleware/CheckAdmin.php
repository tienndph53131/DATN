<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('client')->user();
        if (!$user || $user->role_id != 1 && $user->role_id != 3) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này ');
        }
        return $next($request);
    }
}
