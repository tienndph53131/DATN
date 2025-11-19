<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Lấy guard đầu tiên từ danh sách các guard của route
            $guard = Arr::get($request->route()->middleware(), 'auth');

            if ($guard === 'admin') {
                return route('admin.login');
            }

            return route('client.login');
        }
        return null;
    }
}