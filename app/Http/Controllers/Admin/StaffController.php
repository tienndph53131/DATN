<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::where('role_id', 3);
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }
        $staffs = $query->paginate(10);
        return view('admin.staff.index', compact('staffs'));
    }
    public function create()
    {
        // $currentUser = auth()->guard('client')->user();
        // if ($currentUser->role_id != 1) {
        //     abort(403, 'Bạn không có quyền truy cập');
        // }
        return view('admin.staff.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:accounts,email',
            'phone' => 'nullable|string|max:20',
            // 'role_id' => 'nullable|exists:roles,id',
            // 'status' => 'required|in:0,1',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'password'=>'required|min:6'
        ]);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/staff'), $filename);
            $validated['avatar'] = 'uploads/staff/' . $filename;
        };
        $validated['password'] = bcrypt($validated['password']);
        $validated['role_id'] = 3;
        $validated['status'] = 1;
        Account::create($validated);
        return redirect()->route('staff.index')->with('success', 'Tạo nhân viên thành công!');
    }
    public function show($id)
    {
        $currentUser = auth()->guard('client')->user();
        if ($currentUser->role_id != 1) {
            abort(403, 'Bạn không có quyền truy cập');
        }
        $staff = Account::where('role_id', 3)->find($id);
        return view('admin.staff.show', compact('currentUser', 'staff'));
    }
    public function edit($id)
    {
        $currentUser = auth()->guard('client')->user();
        if ($currentUser->role_id != 1) {
            abort(403, 'Bạn không có quyền truy cập');
        }
        $staff = Account::where('role_id', '3')->find($id);
        $roles = Role::where('name', '!=', 'admin')->get();
        return view('admin.staff.edit', compact('currentUser', 'staff', 'roles'));
    }
    public function update(Request $request, $id)
    {
        $staff = Account::where('role_id', '3')->find($id);
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:accounts,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'required|in:0,1',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/staff'), $filename);
            $validated['avatar'] = 'uploads/staff/' . $filename;
        };
        $staff->update($validated);
        return redirect()->route('staff.index')->with('success', 'Cập nhật nhân viên thành công!');
    }
    public function destroy($id)
    {
        $currentUser = auth()->guard('client')->user();
        if ($currentUser->role_id != 1) {
            abort(403, 'Bạn không có quyền truy cập');
        }
        $staff = Account::where('role_id', 3)->find($id);
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Cập nhật nhân viên thành công!');
    }
}
