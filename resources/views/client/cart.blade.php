@extends('layouts.partials.client')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-4 text-uppercase">Giỏ hàng của bạn</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    @if(empty($cart) || (is_iterable($cart) && count($cart) === 0))
        <div class="text-center py-5">
            <p>Giỏ hàng trống.</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @else
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $key => $item)
                    @php
                        // Nếu là session => $item là mảng
                        $isArray = is_array($item);

                        $variantId = $isArray ? $key : $item->product_variant_id;
                        $image = $isArray 
                            ? asset('uploads/products/' . $item['image'])
                            : asset('uploads/products/' . ($item->productVariant->product->image ?? 'default.jpg'));
                        $productName = $isArray 
                            ? $item['product_name']
                            : $item->productVariant->product->name;
                        $variant = $isArray 
                            ? $item['variant']
                            : $item->productVariant->attributeValues->pluck('value')->join(', ');
                        $price = $isArray ? $item['price'] : $item->price;
                        $quantity = $isArray ? $item['quantity'] : $item->quantity;
                        $subtotal = $isArray ? $item['price'] * $item['quantity'] : $item->amount;
                    @endphp

                    <tr>
                        <td width="100">
                            <img src="{{ $image }}" class="img-fluid rounded" alt="">
                        </td>
                        <td class="text-start">
                            <div class="fw-bold">{{ $productName }}</div>
                            <div class="text-muted small">{{ $variant }}</div>
                        </td>
                        <td>{{ number_format($price, 0, ',', '.') }}₫</td>
                        <td width="120">
                            <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="variant_id" value="{{ $variantId }}">
                                <input type="number" name="quantity" value="{{ $quantity }}" min="1" class="form-control text-center">
                                <button class="btn btn-sm btn-outline-primary mt-2">Cập nhật</button>
                            </form>
                        </td>
                        <td>{{ number_format($subtotal, 0, ',', '.') }}₫</td>
                        <td>
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="variant_id" value="{{ $variantId }}">
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
            <h4 class="mb-3 mb-md-0">
                Tổng cộng: <strong>{{ number_format($total, 0, ',', '.') }}₫</strong>
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ url('/') }}" class="btn btn-outline-dark fw-bold text-uppercase px-4">
                    Tiếp tục mua sắm
                </a>
                <a href="#" class="btn btn-success fw-bold text-uppercase px-4">
                    Thanh toán
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
