<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng ký
     */
    public function showRegister()
    {
        return view('client.auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:accounts,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        // Gán role mặc định là user (role_id = 2)
        Account::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2,
        ]);

        return redirect()->route('client.login')->with('success', 'Đăng ký thành công! Mời bạn đăng nhập.');
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function showLogin()
    {
        return view('client.auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);
         // Thêm điều kiện status = 1
    $credentials = [
        'email' => $request->email,
        'password' => $request->password,
        'status' => 1, // chỉ cho phép tài khoản đang hoạt động
    ];


       

        if (Auth::guard('client')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('client')->user();

            // Nếu là admin → vào trang admin
           if ($user->role_id == 1 || $user->role_id == 3) {
               return redirect()->route('admin.dashboard')->with('success', 'Chào mừng Admin:' . $user->name);            }
            

            // Nếu là user → về trang chủ
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác hoặc tài khoản đã bị vô hiệu hóa.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login')->with('success', 'Đăng xuất thành công!');
    }
}
