@extends('layouts.partials.client')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-4">

    <h3 class="fw-bold mb-4">Chi tiết đơn hàng: {{ $order->order_code }}</h3>

    <div class="mb-4">
        <h5>Thông tin giao hàng</h5>
        <p><strong>Người nhận:</strong> {{ $order->name }}</p>
        <p><strong>SĐT:</strong> {{ $order->phone }}</p>
       <p><strong>Địa chỉ giao hàng:</strong> 
    {{ $order->ghn_address['address_detail'] ?? '' }},
    {{ $order->ghn_address['ward'] ?? '' }},
    {{ $order->ghn_address['district'] ?? '' }},
    {{ $order->ghn_address['province'] ?? '' }}
</p>
        <p><strong>Phương thức thanh toán:</strong> {{ $order->payment->payment_method_name ?? '---' }}</p>
        <p><strong>Trạng thái đơn hàng:</strong> 
      
    @php
        $status = $order->status->status_name ?? '---';
        $statusClass = match($status) {
           'Chưa xác nhận' => 'badge bg-secondary',
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
    @endphp

    <span class="{{ $statusClass }}">{{ $status }}</span>
</p>
<!-- payment status hidden for client detail -->
{{-- Nút hủy đơn chỉ hiển thị nếu chưa xác nhận --}}
@if($order->status_id == 1)
    <form action="{{ route('order.cancel', $order->order_code) }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?')">
            Hủy đơn hàng
        </button>
    </form>
@endif
    </div>

    <h5>Danh sách sản phẩm</h5>
    {{-- Return request feature removed --}}
    <table class="table table-bordered align-middle">
        <thead class="table-secondary">
        <tr>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tạm tính</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->details as $detail)
            <tr>
               <td>
    {{ $detail->product->name ?? 'Sản phẩm đã xóa' }}
    @if($detail->variant)
        <br>
        @foreach($detail->variant->attributeValues as $attr)
            <small>{{ $attr->attribute->name ?? '' }}: {{ $attr->value ?? '' }}</small>@if(!$loop->last), @endif
        @endforeach
    @endif
</td>

                <td>{{ number_format($detail->price) }} đ</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ number_format($detail->amount) }} đ</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="text-end">
        <h4 class="fw-bold">Tổng tiền: {{ number_format($order->total) }} đ</h4>
    </div>

</div>

{{-- Return request modal removed. --}}
@endsection
