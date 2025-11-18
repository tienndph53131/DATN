<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Server-side filtering: q (order code or customer name), status (status_id)
        $q = $request->query('q');
        $status = $request->query('status');

        $query = Order::with('account', 'details.productVariant.product');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('order_code', 'like', "%{$q}%")
                    ->orWhereHas('account', function ($a) use ($q) {
                        $a->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($status) {
            $query->where('status_id', $status);
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->query());

        $statuses = OrderStatus::orderBy('id')->get();
        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show($id)
    {
        $order = Order::with(['details.productVariant.product', 'account', 'address', 'payment', 'status', 'paymentStatus'])->findOrFail($id);
        $statuses = OrderStatus::orderBy('id')->get();
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'nullable|integer|exists:order_status,id'
        ]);

        $order = Order::findOrFail($id);
        $old = $order->status_id;
        $newStatusId = $request->input('status_id');

        // Enforce transition rules from config/order.php
        $fromName = $order->status->status_name ?? null;
        $toStatus = $newStatusId ? OrderStatus::find($newStatusId) : null;
        $toName = $toStatus->status_name ?? null;

        $transitions = config('order.status_transitions', []);
        $allowed = true; // allow by default
        if ($fromName && array_key_exists($fromName, $transitions)) {
            $allowedList = $transitions[$fromName] ?? [];
            // if allowed list is empty => no further transitions allowed
            $allowed = in_array($toName, $allowedList, true);
        }

        if (! $allowed) {
            $msg = 'Chuyển trạng thái từ "' . ($fromName ?? '—') . '" sang "' . ($toName ?? '—') . '" không được phép.';
            if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json(['message' => $msg], 422);
            }
            return redirect()->back()->withErrors(['status_id' => $msg]);
        }

        $order->status_id = $newStatusId;
        $order->save();

        // reload status relation
        $order->load('status');

        // map status name to a badge class (same logic as view)
        $statusName = $order->status->status_name ?? '';
        $statusClass = match($statusName) {
            'Chưa xác nhận' => 'badge bg-secondary',
            'Đã thanh toán, chờ xác nhận' => 'badge bg-primary',
            'Đã xác nhận' => 'badge bg-primary',
            'Đang chuẩn bị hàng' => 'badge bg-info text-dark',
            'Đang giao' => 'badge bg-warning text-dark',
            'Đã giao' => 'badge bg-success',
            'Đã nhận' => 'badge bg-success',
            'Thành công' => 'badge bg-success',
            'Hoàn hàng' => 'badge bg-danger',
            'Hủy đơn hàng' => 'badge bg-dark',
            default => 'badge bg-light text-dark',
        };

        // If AJAX/JSON request, return JSON with new status info
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'order_id' => $order->id,
                'status_id' => $order->status_id,
                'status_name' => $statusName,
                'status_class' => $statusClass,
                'old_status_id' => $old,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
