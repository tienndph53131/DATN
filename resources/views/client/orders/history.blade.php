@extends('layouts.partials.client')

@section('title', 'Lịch sử mua hàng')

@section('content')
<div class="container py-4">

    <h3 class="fw-bold mb-4">Lịch sử mua hàng</h3>

    @if($orders->count() == 0)
        <p>Bạn chưa có đơn hàng nào.</p>
    @else
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
            <tr>
                <th>Mã đơn hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                 <th>Phương thức thanh toán</th>
                <th>Trạng thái đơn hàng</th>
                <th>Trạng thái thanh toán</th>
                <th>Chi tiết</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->booking_date ?? '---' }}</td>
                    <td>{{ number_format($order->total) }} đ</td>
                     <td>{{ $order->payment->payment_method_name ?? '---' }}</td> 

                    <td>
                        <span class="{{ $statusColors[$order->status->status_name] ?? 'text-dark' }}">{{ $order->status->status_name ?? '---' }}</span>
                    </td>
                    <td>
                        <span class="
                            {{ $order->paymentStatus && $order->paymentStatus->id == 2 ? 'text-success fw-semibold' :  'text-warning fw-semibold'}}">
                            {{ $order->paymentStatus->status_name ?? '---' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('order.history.detail', $order->order_code) }}"
                           class="btn btn-sm btn-info">Xem</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
