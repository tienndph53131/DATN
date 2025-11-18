<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderStatus;

class OrderStatusController extends Controller
{
    public function index(Request $request)
    {
        $statuses = OrderStatus::orderBy('id')->paginate(20);
        return view('admin.order_statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('admin.order_statuses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'status_name' => 'required|string|max:255|unique:order_status,status_name',
        ]);

        OrderStatus::create($data);
        return redirect()->route('order-statuses.index')->with('success', 'Thêm trạng thái thành công.');
    }

    public function edit($id)
    {
        $status = OrderStatus::findOrFail($id);
        return view('admin.order_statuses.edit', compact('status'));
    }

    public function update(Request $request, $id)
    {
        $status = OrderStatus::findOrFail($id);
        $data = $request->validate([
            'status_name' => 'required|string|max:255|unique:order_status,status_name,' . $status->id,
        ]);

        $status->update($data);
        return redirect()->route('order-statuses.index')->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function destroy($id)
    {
        $status = OrderStatus::findOrFail($id);
        // prevent deleting if in use
        if ($status->orders()->count() > 0) {
            return redirect()->back()->withErrors(['msg' => 'Không thể xóa trạng thái đang được sử dụng bởi đơn hàng.']);
        }
        $status->delete();
        return redirect()->route('order-statuses.index')->with('success', 'Xóa trạng thái thành công.');
    }
}
