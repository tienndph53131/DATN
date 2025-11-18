<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\Auth;
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
        $paymentStatuses = PaymentStatus::orderBy('id')->get();
        return view('admin.orders.index', compact('orders', 'statuses', 'paymentStatuses'));
    }

    public function show($id)
    {
        $order = Order::with(['details.productVariant.product', 'account', 'address', 'payment', 'status', 'paymentStatus'])->findOrFail($id);
        $statuses = OrderStatus::orderBy('id')->get();
        // load status change logs
        $logs = OrderStatusLog::with(['oldStatus', 'newStatus', 'user'])
            ->where('order_id', $order->id)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.orders.show', compact('order', 'statuses', 'logs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|integer|exists:order_status,id'
        ]);

        $request->validate([
            'payment_status_id' => 'nullable|integer|exists:payment_status,id'
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
        // handle payment status change if provided
        $oldPayment = $order->payment_status_id;
        $newPaymentId = $request->input('payment_status_id');

        if ($newPaymentId !== null && intval($newPaymentId) !== intval($oldPayment)) {
            $order->payment_status_id = $newPaymentId;
        }

        $order->save();

        // create audit log for status change
        try {
            OrderStatusLog::create([
                'order_id' => $order->id,
                'old_status_id' => $old,
                'new_status_id' => $order->status_id,
                'changed_by' => Auth::id(),
                'note' => null,
            ]);

            // record a simple note for payment status change as well
            if ($newPaymentId !== null && intval($newPaymentId) !== intval($oldPayment)) {
                $oldName = null;
                $newName = null;
                try {
                    $oldName = $oldPayment ? \App\Models\PaymentStatus::find($oldPayment)?->status_name : null;
                    $newName = \App\Models\PaymentStatus::find($newPaymentId)?->status_name;
                } catch (\Throwable $_) {}

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'old_status_id' => null,
                    'new_status_id' => null,
                    'changed_by' => Auth::id(),
                    'note' => sprintf('Payment status: "%s" -> "%s"', $oldName ?? ($oldPayment ?: '—'), $newName ?? $newPaymentId),
                ]);
            }
        } catch (\Throwable $e) {
            logger()->error('Failed to write order status log: ' . $e->getMessage());
        }

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

        // reload payment status relation too
        $order->load('status', 'paymentStatus');

        // If AJAX/JSON request, return JSON with new status info
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            $paymentName = $order->paymentStatus->status_name ?? null;
            $paymentClass = config('payment.status_classes')[$paymentName] ?? 'badge bg-light text-dark';

            return response()->json([
                'order_id' => $order->id,
                'status_id' => $order->status_id,
                'status_name' => $statusName,
                'status_class' => $statusClass,
                'old_status_id' => $old,
                'payment_status_id' => $order->payment_status_id,
                'payment_status_name' => $paymentName,
                'payment_status_class' => $paymentClass,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
