<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order; // Import model Order
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Hiển thị lịch sử đơn hàng của người dùng.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lấy ID của người dùng đã đăng nhập qua guard 'client'
        $accountId = auth('client')->id();

        // Lấy danh sách đơn hàng của người dùng đó, sắp xếp mới nhất lên đầu
        // Sử dụng paginate để phân trang, giúp trang không bị tải chậm nếu có nhiều đơn hàng
        $orders = Order::where('account_id', $accountId)
                       ->latest('booking_date') // Sắp xếp theo ngày đặt hàng, mới nhất trước
                       ->paginate(10); // Lấy 10 đơn hàng mỗi trang

        // Trả về view 'client.orders.index' và truyền biến $orders sang
        return view('client.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        // Tìm đơn hàng theo ID, nếu không thấy sẽ tự động trả về lỗi 404
        // Sử dụng with() để tải các mối quan hệ, tránh vấn đề N+1 query
        $order = Order::with([
            'account',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.attributeValues.attribute',
            'payment', // Phương thức thanh toán
        ])->findOrFail($id);

        // Trả về view và truyền dữ liệu đơn hàng sang
        // **BẢO MẬT**: Kiểm tra xem người dùng có quyền xem đơn hàng này không
        if (auth('client')->id() !== $order->account_id) {
            abort(403, 'Bạn không có quyền truy cập vào đơn hàng này.');
        }

        // Sửa đường dẫn view sang phía client
        return view('client.show', compact('order'));
    }
}
