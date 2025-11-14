<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // Nếu là admin (role_id == 1) chuyển đến trang quản trị
            if (Auth::user()->role_id == 1) {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended('/');
        }

        return back()->withInput()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:accounts'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Vui lòng nhập họ tên của bạn',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        // Tìm ID của vai trò 'Customer' một cách linh hoạt
        $customerRole = DB::table('roles')->where('name', 'Customer')->first();
        if (!$customerRole) {
            // Xử lý trường hợp không tìm thấy vai trò, có thể báo lỗi hoặc tạo mặc định
            return back()->withErrors(['email' => 'Lỗi hệ thống: Không tìm thấy vai trò người dùng.'])->withInput();
        }

        $account = Account::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $customerRole->id, // Gán role_id một cách linh hoạt
        ]);

        Auth::login($account);

        // Bỏ qua bước gửi email xác thực
        // $account->sendEmailVerificationNotification();

        return redirect()->intended('/'); // Chuyển hướng về trang chủ sau khi đăng ký thành công
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // public function resendVerificationEmail(Request $request)
    // {
    //     if ($request->user()->hasVerifiedEmail()) {
    //         return redirect()->intended('/');
    //     }
    //     $request->user()->sendEmailVerificationNotification();
    //     return back()->with('resent', true);
    // }
}