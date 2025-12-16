<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    // store comment for a product (requires client auth)
    public function store(Request $request, $productId)
    {
    $request->validate([
        'content' => 'required|string|max:2000',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    $accountId = Auth::guard('client')->id();
    if (!$accountId) {
        return redirect()->route('client.login')
            ->with('error', 'Bạn cần đăng nhập để bình luận.');
    }

    //  Kiểm tra đã mua sản phẩm chưa
    $hasPurchased = DB::table('orders')
        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
        ->where('orders.account_id', $accountId)
        ->where('order_details.product_id', $productId)
        ->whereIn('orders.status_id', [5]) // ví dụ: 5 = Đã nhận
        ->exists();

    if (!$hasPurchased) {
        return redirect()->back()
            ->with('error', 'Bạn chỉ có thể bình luận khi đã mua sản phẩm này.');
    }

    Comment::create([
        'product_id' => $productId,
        'account_id' => $accountId,
        'content' => $request->input('content'),

        'rating' => (int) $request->rating,
        'date' => now(),
        'status' => 1,
    ]);

    return redirect()->back()->with('success', 'Bình luận đã được gửi.');
}
}