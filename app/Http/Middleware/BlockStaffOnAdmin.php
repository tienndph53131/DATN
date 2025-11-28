<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockStaffOnAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('client')->user();


        // Kiểm tra nếu role 3 và truy cập module admin (ví dụ accounts)
        if ($user && $user->role_id == 3) {
            
            // Chỉ block các route liên quan admin account
            if ($request->is('admin/accounts*') && in_array($request->method(), ['POST','PUT','PATCH','DELETE'])) {
                return redirect()->back()->with('error', 'Nhân viên không có quyền thực hiện thao tác này trên quản lý Admin!');
            }
        }

        return $next($request);
    }
}
