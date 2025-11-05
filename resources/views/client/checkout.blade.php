@extends('layouts.partials.client')
@section('title', 'Checkout')
@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #2d3748;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .checkout-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .checkout-header p {
            font-size: 1rem;
            color: #718096;
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 40px;
        }

        /* Left Column - Forms */
        .checkout-form-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f7fafc;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3182ce;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        }

        .form-group input::placeholder {
            color: #a0aec0;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #dcfce7;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fee2e2;
        }

        /* Cart Table */
        .cart-section {
            margin-top: 35px;
            padding-top: 35px;
            border-top: 2px solid #edf2f7;
        }

        .cart-section h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        table thead {
            background-color: #f7fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        table tbody tr:hover {
            background-color: #f7fafc;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            font-size: 1.1rem;
        }

        .cart-total-label {
            font-weight: 600;
            color: #2d3748;
        }

        .cart-total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3182ce;
        }

        /* Buttons Section */
        .buttons-section {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-direction: column;
        }

        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #3182ce;
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2c5aa0;
            box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3);
        }

        .btn-secondary {
            background-color: #edf2f7;
            color: #2d3748;
            width: 100%;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        /* Right Column - Order Summary */
        .order-summary {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .order-summary h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: #2d3748;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            font-size: 0.95rem;
            color: #4a5568;
        }

        .summary-item-label {
            font-weight: 500;
        }

        .summary-divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 20px 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-top: 2px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .summary-total-label {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2d3748;
        }

        .summary-total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3182ce;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
            }

            .checkout-header h1 {
                font-size: 1.75rem;
            }

            .buttons-section {
                flex-direction: column;
            }

            table {
                font-size: 0.85rem;
            }

            table th,
            table td {
                padding: 10px 8px;
            }
        }
    </style>

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Thanh Toán</h1>
            <p>Vui lòng kiểm tra thông tin và hoàn tất đơn hàng của bạn</p>
        </div>

        <div class="checkout-content">
            <!-- Left Column -->
            <div>
                <div class="checkout-form-section">
                    @if (session('success'))
                        <div class="alert alert-success">✓ {{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">✕ {{ session('error') }}</div>
                    @endif

                    <!-- Thông tin khách hàng -->
                    <form action="{{ route('checkout.process') }}" method="post">
                        @csrf
                        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 25px; color: #2d3748;">Thông tin
                            khách hàng</h2>

                        <div class="form-group">
                            <label for="name">Họ và tên</label>
                            <input type="text" name="name" id="name" placeholder="Nhập họ và tên" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Nhập email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" name="phone" id="phone" placeholder="Nhập số điện thoại" required>
                        </div>

                        <div class="form-group">
                            <label for="address_id">Địa chỉ giao hàng</label>
                            <select name="address_id" id="address_id">
                                <option value="">-- Chọn địa chỉ --</option>
                                @foreach ($address as $item)
                                    <option value="{{ $item->id }}">{{ $item->province_name }} - {{ $item->district_name }} -
                                        {{ $item->ward_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <input type="hidden" name="payment_id" value="1"> --}}

                        <!-- Giỏ hàng -->
                        @if (isset($cartDetails) && count($cartDetails) > 0)
                            <div class="cart-section">
                                <h2>Chi tiết đơn hàng</h2>
                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Phân loại</th>
                                                <th>Giá</th>
                                                <th>Số lượng</th>
                                                <th>Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cartDetails as $item)
                                                <tr>
                                                    <td>{{ $item->productVariant->product->name }}</td>
                                                    <td> @foreach ($item->productVariant->attributeValues as $value)
                                                        {{ $value->value }}
                                                    @endforeach
                                                    </td>
                                                    <td>{{ number_format($item->price) }}đ</td>
                                                    <td style="text-align: center;">{{ $item->quantity }}</td>
                                                    <td style="font-weight: 600; color: #3182ce;">
                                                        {{ number_format($item->amount) }}đ
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="cart-total">
                                    <span class="cart-total-label">Tổng cộng:</span>
                                    <span class="cart-total-amount">{{ number_format($total) }}đ</span>
                                </div>
                            </div>

                            <hr>

                            <h3>Phương thức thanh toán</h3>

                            <div class="form-group">
                                <select name="payment_id" required>
                                    <option value="1">COD (Thanh toán khi nhận hàng)</option>
                                    <option value="2">MoMo</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Đặt hàng</button>
                        @endif
                    </form>
                </div>

                <!-- MoMo Payment Form -->
                {{-- <div class="checkout-form-section" style="margin-top: 20px;">
                    <form action="{{ route('momo.payment') }}" method="post">
                        @csrf
                        <input type="hidden" name="total_momo" id="total_momo" value="{{ $total ?? 0 }}">
                        <input type="hidden" name="payment_id" value="2">
                        <button type="submit" class="btn btn-secondary">🏦 Thanh Toán MoMo</button>
                    </form>
                </div> --}}
            </div>

            <!-- Right Column - Order Summary -->
            <div class="order-summary">
                <h3>Tóm tắt đơn hàng</h3>

                @if (isset($cartDetails) && count($cartDetails) > 0)
                    @foreach ($cartDetails as $item)
                        <div class="summary-item">
                            <span class="summary-item-label">{{ $item->productVariant->product->name }}</span>
                            <span style="color: #2d3748; font-weight: 600;">{{ number_format($item->amount) }}đ</span>
                        </div>
                    @endforeach

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span class="summary-total-label">Tổng tiền:</span>
                        <span class="summary-total-amount">{{ number_format($total) }}đ</span>
                    </div>

                    <div
                        style="background-color: #f0f9ff; padding: 12px; border-radius: 8px; font-size: 0.85rem; color: #0369a1; text-align: center; border: 1px solid #bae6fd;">
                        ✓ Miễn phí giao hàng cho đơn hàng từ 500.000đ
                    </div>
                @else
                    <p style="text-align: center; color: #718096; padding: 20px;">Giỏ hàng của bạn trống</p>
                @endif
            </div>
        </div>
    </div>
@endsection