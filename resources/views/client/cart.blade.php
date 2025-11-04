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

        @if(empty($cart))
            <div class="text-center py-5">
                <p>Giỏ hàng trống.</p>
                <a href="{{ url('/') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
            </div>
        @else
            <form action="{{ route('cart.update') }}" method="POST">
                @csrf
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Thuộc tính</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $id => $item)
                            <tr>
                                <td width="100">
                                    <img src="{{ asset('uploads/products/' . $item['image']) }}" class="img-fluid rounded" alt="">
                                </td>
                                <td>{{ $item['product_name'] }}</td>
                                <td>{{ $item['variant'] }}</td>
                                <td>{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                                <td width="120">
                                    <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="variant_id" value="{{ $id }}">
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                            class="form-control text-center">
                                        {{-- <button class="btn btn-sm btn-outline-primary mt-2">Cập nhật</button> --}}
                                    </form>
                                </td>
                                <td>{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</td>
                                <td>
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="variant_id" value="{{ $id }}">
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <h4>Tổng cộng: <strong>{{ number_format($total, 0, ',', '.') }}₫</strong></h4>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary fw-bold text-uppercase px-4">
                    Tiếp tục mua sắm
                </a>
                <a href="#" class="btn btn-success">Thanh toán</a>
            </div>
        @endif
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qtyInput = document.querySelectorAll('input[name="quantity"]'); // lay input quantity tat ca cac trang
        qtyInput.forEach(input => { // gan su kien thay doi so luong 
            input.addEventListener("change", function () {
                this.closest("form").submit();
            });
        });
    });
</script>