@extends('client.layouts.app') {{-- Giả sử bạn có layout chung cho client --}}

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            {{-- Có thể thêm menu cho trang cá nhân ở đây --}}
            @include('client.profile_sidebar')
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Chi tiết đơn hàng #{{ $order->order_code }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin chung</h5>
                            <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y H:i') }}</p>
                            <p><strong>Trạng thái:</strong>
                                {{-- Tạm thời hiển thị ID trạng thái để tránh lỗi. 
                                     Sẽ cần tạo bảng order_statuses và model để hiển thị tên. --}}
                                @php
                                    $statusMap = [
                                        1 => ['name' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
                                        2 => ['name' => 'Đã thanh toán', 'class' => 'bg-success'],
                                        // Thêm các trạng thái khác tại đây
                                    ];
                                    $statusInfo = $statusMap[$order->status_id] ?? ['name' => 'Không xác định', 'class' => 'bg-secondary'];
                                @endphp
                                <span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['name'] }}</span>
                            </p>
                            <p><strong>Phương thức thanh toán:</strong> {{ optional($order->payment)->payment_method_name }}</p>
                            <p><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Địa chỉ giao hàng</h5>
                            @if ($order->orderAddress)
                                <p><strong>Người nhận:</strong> {{ $order->orderAddress->recipient_name }}</p>
                                <p><strong>Điện thoại:</strong> {{ $order->orderAddress->phone }}</p>
                                <p><strong>Địa chỉ:</strong> {{ $order->orderAddress->address_line }}, {{ $order->orderAddress->ward }}, {{ $order->orderAddress->district }}, {{ $order->orderAddress->province }}</p>
                            @else
                                <p>Không có thông tin địa chỉ.</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <h5>Sản phẩm đã đặt</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $item)
                                    <tr>
                                        <td>
                                            @if ($item->productVariant)
                                                <p class="mb-0 fw-bold">{{ optional($item->productVariant->product)->name ?? '[Sản phẩm không còn tồn tại]' }}</p>
                                                <small class="text-muted">
                                                    @foreach ($item->productVariant->attributeValues as $attrValue)
                                                        {{ $attrValue->attribute->name }}: {{ $attrValue->value }}@if (!$loop->last), @endif
                                                    @endforeach
                                                </small>
                                            @else
                                                <p class="mb-0 fw-bold text-danger">[Sản phẩm không còn tồn tại]</p>
                                                <small class="text-muted">ID biến thể: {{ $item->product_variant_id }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                                        <td class="text-end">{{ number_format($item->amount, 0, ',', '.') }} ₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tạm tính</strong></td>
                                    <td class="text-end">{{ number_format($order->orderDetails->sum('amount'), 0, ',', '.') }} ₫</td>
                                </tr>
                                @if ($order->discount)
                                <tr>
                                    <td colspan="3" class="text-end">
                                        <strong>Giảm giá ({{ $order->discount->discount_code }})</strong>
                                    </td>
                                    <td class="text-end">- {{ number_format($order->orderDetails->sum('amount') - $order->total, 0, ',', '.') }} ₫</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end fw-bold fs-5">Tổng cộng</td>
                                    <td class="text-end fw-bold fs-5">{{ number_format($order->total, 0, ',', '.') }} ₫</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('client.profile.edit') }}" class="btn btn-secondary">Quay lại trang cá nhân</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Bạn có thể thêm CSS tùy chỉnh tại đây nếu cần */
    .card-header h3 {
        font-size: 1.5rem;
    }
</style>
@endpush
