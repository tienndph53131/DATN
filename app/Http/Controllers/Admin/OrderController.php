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
        $newStatusId = $request->input('status_id');
        $newPaymentId = $request->input('payment_status_id');

        $service = new \App\Services\OrderStatusService();
        $result = $service->changeStatus($order, $newStatusId, $newPaymentId, Auth::id());

        if (! $result['ok']) {
            if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json(['message' => $result['message']], 422);
            }
            return redirect()->back()->withErrors(['status_id' => $result['message']]);
        }

        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json($result['data']);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}

// OrderStatusController moved here to group admin order-related controllers in one file
class OrderStatusController extends Controller
{
    public function index(Request $request)
    {
        $statuses = \App\Models\OrderStatus::orderBy('id')->paginate(20);
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

        \App\Models\OrderStatus::create($data);
        return redirect()->route('order-statuses.index')->with('success', 'Thêm trạng thái thành công.');
    }

    public function edit($id)
    {
        $status = \App\Models\OrderStatus::findOrFail($id);
        return view('admin.order_statuses.edit', compact('status'));
    }

    public function update(Request $request, $id)
    {
        $status = \App\Models\OrderStatus::findOrFail($id);
        $data = $request->validate([
            'status_name' => 'required|string|max:255|unique:order_status,status_name,' . $status->id,
        ]);

        $status->update($data);
        return redirect()->route('order-statuses.index')->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function destroy($id)
    {
        $status = \App\Models\OrderStatus::findOrFail($id);
        if ($status->orders()->count() > 0) {
            return redirect()->back()->withErrors(['msg' => 'Không thể xóa trạng thái đang được sử dụng bởi đơn hàng.']);
        }
        $status->delete();
        return redirect()->route('order-statuses.index')->with('success', 'Xóa trạng thái thành công.');
    }
}
