<h3>Đặt hàng thành công!</h3>

<p>Chào {{ $order->name }},</p>

<p>Đơn hàng <strong>{{ $order->order_code }}</strong> của bạn đã được đặt thành công.</p>

<h5>Thông tin đơn hàng:</h5>
<ul>
    <li>Ngày đặt: {{ $order->booking_date }}</li>
    <li>Tổng tiền: {{ number_format($order->total) }} đ</li>
    <li>Phương thức thanh toán: {{ $order->payment->payment_method_name ?? '---' }}</li>
    <li>Trạng thái: {{ $order->status->status_name ?? '---' }}</li>
</ul>

<h5>Danh sách sản phẩm:</h5>
<ul>
@foreach($order->details as $detail)
    <li>{{ $detail->product->name ?? 'Sản phẩm đã xóa' }} x {{ $detail->quantity }} = {{ number_format($detail->amount) }} đ</li>
@endforeach
</ul>

<p>Cảm ơn bạn đã mua hàng!</p>
