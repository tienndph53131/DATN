@extends('layouts.partials.client')

@section('title', 'Lịch sử đơn hàng')

@section('content')
<div class="container mt-5">
    <h1>Lịch sử đơn hàng</h1>

    @if(count($orders) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $order->booking_date }}</td>
                        <td>{{ number_format($order->total) }}₫</td>
                        <td>{{ $order->status_id }}</td>
                        <td>
                            <a href="{{ route('client.orders.show', $order->id) }}">Xem chi tiết</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $orders->links() }} {{-- Hiển thị phân trang --}}
    @else
        <p>Bạn chưa có đơn hàng nào.</p>
    @endif
</div>
@endsection


{{-- Thêm CSS nếu cần --}}
@section('styles')
<style>
    /* Tùy chỉnh CSS nếu cần */
</style>
@endsection


{{-- Thêm JS nếu cần --}}
@section('scripts')
<script>
    // Thêm JS nếu cần
</script>
@endsection
