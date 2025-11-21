<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Role;

class AccountController extends Controller
{
    // 📋 Danh sách accounts + tìm kiếm
    public function index(Request $request)
    {
        $query = Account::query();

        // Ẩn tài khoản admin
        $query->whereDoesntHave('role', function ($q) {
            $q->where('name', 'admin');
        });

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $accounts = $query->paginate(10);
        return view('admin.accounts.index', compact('accounts'));
    }

    // 👁 Xem chi tiết
    public function show($id)
    {
        $currentUser = auth()->guard('client')->user();
        if ($currentUser->role_id != 1) {
            abort(403, 'Bạn không có quyền truy cập');
        }
        $account = Account::findOrFail($id);

        // Ẩn admin (không cho xem thông tin admin)
        if ($account->role && $account->role->name === 'admin') {
            return redirect()->route('accounts.index')->with('error', 'Không thể xem thông tin tài khoản admin!');
        }

        return view('admin.accounts.show', compact('account', 'currentUser'));
    }

    // 🖋 Form sửa
    public function edit($id)
    {
        $currentUser = auth()->guard('client')->user();
        if ($currentUser->role_id != 1) {
            abort(403, 'Bạn không có quyền truy cập');
        }
        $account = Account::findOrFail($id);
        $roles = Role::where('name', '!=', 'admin')->get();
        return view('admin.accounts.edit', compact('account', 'roles', 'currentUser'));
    }

    // 💾 Cập nhật
    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:accounts,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'required|in:0,1',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Nếu có upload ảnh mới
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $filename);
            $validated['avatar'] = 'uploads/products/' . $filename; // Lưu đường dẫn tương đối
        }

        $account->update($validated);

        return redirect()->route('accounts.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    // 🗑️ Xóa
    public function destroy($id)
    {
        $currentUser = auth()->guard('client')->user();
        if ($currentUser->role_id != 1) {
            abort(403, 'Bạn không có quyền xóa');
        }
        $account = Account::findOrFail($id);
        if ($account->role && $account->role->name === 'admin') {
            return redirect()->route('accounts.index')->with('error', 'Không thể xóa tài khoản admin!');
        }

        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Xóa tài khoản thành công!');
    }
}
