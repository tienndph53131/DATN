<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('account', 'details.productVariant.product')->get();
        $status = OrderStatus::all();
        return view('admin.orders.index', compact('orders','status'));
    }
}