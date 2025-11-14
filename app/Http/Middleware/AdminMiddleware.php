<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            // Nếu chưa đăng nhập hoặc không phải admin, chuyển về form đăng nhập client
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập vào trang quản trị.');
        }

        return $next($request);
    }
}