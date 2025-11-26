<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('client')->user();

        if ($user->role_id != 1) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này.');
        }

        return $next($request);
    }
}
