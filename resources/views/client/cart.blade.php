@extends('layouts.partials.client')

@section('title', 'Giỏ hàng')

@section('content')
    <div class="container py-5">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <h2 class="fw-bold text-center mb-4 text-uppercase">Giỏ hàng của bạn</h2>

        @if(empty($cart) || count($cart) === 0)
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
                    @foreach($cart as $item)
                        @php
                            $variantId = $item->product_variant_id;
                            $image = asset('uploads/products/' . ($item->productVariant->product->image ?? 'default.jpg'));
                            $productName = $item->productVariant->product->name;
                            $variant = $item->productVariant->attributeValues->pluck('value')->join(', ');
                            $price = $item->price;
                            $quantity = $item->quantity;
                            $subtotal = $item->amount;
                            $stock = $item->productVariant->stock_quantity;
                        @endphp

                        <tr data-variant="{{ $variantId }}" data-stock="{{ $stock }}">
                            <td width="100">
                                <img src="{{ $image }}" class="img-fluid rounded" alt="">
                            </td>
                            <td class="text-start">
                                <div class="fw-bold">{{ $productName }}</div>
                                <div class="text-muted small">{{ $variant }}</div>
                            </td>
                            <td class="price" data-price="{{ $price }}">{{ number_format($price, 0, ',', '.') }}₫</td>
                            <td width="120">
                                <input type="number" class="form-control text-center quantity-input" value="{{ $quantity }}"
                                    min="1">
                                <div class="text-danger small stock-error" style="display:none;">Số lượng sản phẩm khômg đủ</div>
                            </td>
                            <td class="subtotal">{{ number_format($subtotal, 0, ',', '.') }}₫</td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-btn">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                <h4 class="mb-3 mb-md-0">
                    Tổng cộng: <strong id="totalAmount">{{ number_format($total, 0, ',', '.') }}₫</strong>
                </h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('/') }}" class="btn btn-outline-dark fw-bold text-uppercase px-4">
                        Tiếp tục mua sắm
                    </a>
                    <a href="{{ route('checkout.index') }}" class="btn btn-success fw-bold text-uppercase px-4">
                        Thanh toán
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';

            function updateQuantity(tr, quantity) {
                const variantId = tr.dataset.variant;
                const stock = parseInt(tr.dataset.stock);
                const price = parseFloat(tr.querySelector('.price').dataset.price);
                const errorDiv = tr.querySelector('.stock-error');

                // Kiểm tra số lượng trước khi gửi AJAX
                if (quantity > stock) {
                    errorDiv.style.display = 'block';
                    return;
                } else {
                    errorDiv.style.display = 'none';
                }

                fetch("{{ route('cart.update') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        variant_id: variantId,
                        quantity: quantity
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            tr.querySelector('.subtotal').textContent = new Intl.NumberFormat('vi-VN').format(price * quantity) + '₫';
                            document.getElementById('totalAmount').textContent = new Intl.NumberFormat('vi-VN').format(data.total) + '₫';
                        } else {
                            errorDiv.textContent = data.message;
                            errorDiv.style.display = 'block';
                        }
                    })
                    .catch(err => console.error(err));
            }

            // Bắt sự kiện change input
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function () {
                    const tr = this.closest('tr');
                    let quantity = parseInt(this.value);
                    if (isNaN(quantity) || quantity < 1) quantity = 1;
                    this.value = quantity;
                    updateQuantity(tr, quantity);
                });
            });

            // Xóa sản phẩm
            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    if (!confirm('Xóa sản phẩm này?')) return;
                    const tr = this.closest('tr');
                    const variantId = tr.dataset.variant;

                    fetch("{{ route('cart.remove') }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ variant_id: variantId })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                tr.remove();
                                document.getElementById('totalAmount').textContent = new Intl.NumberFormat('vi-VN').format(data.total) + '₫';
                            } else {
                                alert('Xóa thất bại!');
                            }
                        });
                });
            });
        });
    </script>
@endsection