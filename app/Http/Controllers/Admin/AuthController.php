<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) { // Kiểm tra xem admin đã đăng nhập chưa
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) { // Thử đăng nhập bằng guard 'admin'
            if (Auth::guard('admin')->user()->role_id != 1) { // Kiểm tra role_id sau khi đăng nhập thành công
                Auth::guard('admin')->logout(); // Đăng xuất khỏi guard 'admin'
                return back()->withErrors([
                    'email' => 'Bạn không có quyền truy cập vào trang quản trị.',
                ]);
            }
            
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Đăng xuất khỏi guard 'admin'
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login'); // Chuyển hướng về trang đăng nhập admin
    }
}