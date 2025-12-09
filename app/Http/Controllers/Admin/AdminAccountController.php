<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminAccountController extends Controller
{
    public function __construct()
    {
        // Middleware chặn nhân viên thao tác POST/PUT/PATCH/DELETE
        $this->middleware('block.staff.admin')->only(['store','update','destroy']);
    }

    // Danh sách tài khoản (chỉ Admin + Nhân viên)
    public function index()
    {
        $accounts = Account::whereIn('role_id', [1,3])->get();
        return view('admin.accountsadmin.index', compact('accounts'));
    }

    // Xem chi tiết
    public function show($id)
    {
        
        $account = Account::findOrFail($id);
        return view('admin.accountsadmin.show', compact('account'));
    }

    // Form thêm mới
    public function create()
    {
        if (Auth::guard('client')->user()->role_id != 1) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }
        return view('admin.accountsadmin.create');
    }

    // Lưu tài khoản mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:accounts,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|in:1,3',
            'status' => 'required|boolean',
        ]);

        Account::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('accountsadmin.index')->with('success', 'Tài khoản đã được tạo!');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $account = Account::findOrFail($id);
        return view('admin.accountsadmin.edit', compact('account'));
    }

    // Cập nhật tài khoản
    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:accounts,email,'.$account->id,
            'role_id' => 'required|in:1,3',
            'status' => 'required|boolean',
        ]);

        $account->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $account->id == 1 ? 1 : $request->status, // admin mặc định luôn active
        ]);

        return redirect()->route('accountsadmin.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

  
}
