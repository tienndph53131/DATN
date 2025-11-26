<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Account; // model tài khoản (user)
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Lấy khoảng thời gian lọc, mặc định tháng hiện tại
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1️⃣ Tổng doanh thu (chỉ đơn hoàn thành, status_id = 5)
        $totalRevenue = Order::where('status_id', 5)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // 2️⃣ Doanh thu theo ngày
        $revenueByDay = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->where('status_id', 5)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3️⃣ Top 10 user đặt hàng nhiều nhất
        $topUsers = Order::selectRaw('account_id, COUNT(*) as order_count, SUM(total) as total_spent')
            ->where('status_id', 5)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('account_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->with('account') // quan hệ Account
            ->get();

        // 4️⃣ Top 10 sản phẩm bán chạy
        $topProducts = OrderDetail::selectRaw('product_id, SUM(quantity) as sold_qty')
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->where('status_id', 5)
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderByDesc('sold_qty')
            ->limit(10)
            ->with('product')
            ->get();

        // 5️⃣ Top 10 sản phẩm ế nhất
        $unsoldProducts = Product::withCount(['orderDetails as sold_qty' => function($q) use ($startDate, $endDate) {
            $q->whereHas('order', function($q2) use ($startDate, $endDate) {
                $q2->where('status_id', 5)
                   ->whereBetween('created_at', [$startDate, $endDate]);
            });
        }])
        ->orderBy('sold_qty')
        ->limit(10)
        ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'revenueByDay',
            'topUsers',
            'topProducts',
            'unsoldProducts',
            'startDate',
            'endDate'
        ));
    }
}
