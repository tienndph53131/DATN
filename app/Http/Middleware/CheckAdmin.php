<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('client')->user();


        // Nếu chưa đăng nhập hoặc không thuộc role admin (1,3)
        if (!$user || !in_array($user->role_id, [1, 3])) {
            return redirect()->route('home')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
