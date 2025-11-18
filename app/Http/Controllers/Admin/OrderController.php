<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('account', 'address', 'payment')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['details.productVariant.product', 'account', 'address', 'payment'])->findOrFail($id);
        $statuses = OrderStatus::orderBy('id')->get();
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'nullable|integer|exists:order_status,id'
        ]);

        $order = Order::findOrFail($id);
        $order->status_id = $request->status_id;
        $order->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
