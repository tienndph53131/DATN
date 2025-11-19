<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Nếu route yêu cầu xác thực với guard 'admin', chuyển hướng đến trang login của admin.
        if (Route::is('admin.*')) {
            return route('admin.login');
        }

        // Mặc định, chuyển hướng đến trang login của client.
        return route('client.login');
    }
}
