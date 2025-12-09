@extends('layouts.partials.client')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-4">
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

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
           'Chưa xác nhận'   => '',
           'Đã xác nhận'   => 'text-primary',
           'Đang giao'    => 'text-info',
            'Đã giao'     => 'text-success',
             'Đã nhận'  => 'text-success fw-bold',
             'Hoàn hàng'  => 'text-warning',
           'Hủy đơn hàng'  => 'text-danger',
              default  => 'text-dark',

        };
    @endphp

    <span class="{{ $statusClass }}">{{ $status }}</span>
</p>
@php
    $paymentStatus = $order->paymentStatus->status_name ?? '---';
    $paymentClass = match($paymentStatus) {
        'Chưa thanh toán' => 'text-warning fw-semibold',
          'Đã thanh toán' => 'text-success fw-semibold',
           default  => 'text-muted',
    };
@endphp

<p><strong>Trạng thái thanh toán:</strong>
    <span class="{{ $paymentClass }}">{{ $paymentStatus }}</span>
</p>
{{-- Nút hủy đơn chỉ hiển thị nếu chưa xác nhận --}}
@if($order->status_id == 1)
    <form action="{{ route('order.cancel', $order->order_code) }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?')">
            Hủy đơn hàng
        </button>
    </form>
@endif
@if($order->status_id == 4)
    <div class="mt-2 d-flex gap-2">

        <!-- Nút Đã nhận -->
        <a href="{{ route('client.order.received', $order->order_code) }}"
           class="btn btn-success btn-sm"
           onclick="return confirm('Xác nhận đã nhận hàng?')">
            Đã nhận
        </a>

        <!-- Nút Hoàn hàng -->
        <a href="{{ route('client.order.return', $order->order_code) }}"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Bạn muốn yêu cầu hoàn hàng?')">
            Hoàn hàng
        </a>

    </div>
@endif

    </div>

    <h5>Danh sách sản phẩm</h5>
    
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
  {{-- Hiển thị giảm giá nếu có --}}
    @if ($order->discount_amount > 0)
        <p class="fs-5 text-success">
            <strong>Giảm giá:</strong> -{{ number_format($order->discount_amount) }} đ
        </p>
    @endif

    <div class="text-end">
        <h4 class="fw-bold">Tổng tiền: {{ number_format($order->total) }} đ</h4>
    </div>

</div>
@endsection
