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
                    <form action="{{ route('client.checkout.process') }}" method="post" id="checkout-form">
                        @csrf
                        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 25px; color: #2d3748;">Thông tin
                            khách hàng</h2>

                        <div class="form-group">
                            <label for="name">Họ và tên</label>
                            <input type="text" name="name" id="name" placeholder="Nhập họ và tên" value="{{ old('name', $account->name ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Nhập email" value="{{ old('email', $account->email ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" name="phone" id="phone" placeholder="Nhập số điện thoại" value="{{ old('phone', $account->phone ?? '') }}" required>
                        </div>

                        <hr style="margin: 30px 0;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 25px; color: #2d3748;">Địa chỉ giao hàng</h2>

                        @if($addresses->count() > 0)
                        <div class="form-group">
                            <label for="saved_address">Chọn địa chỉ đã lưu</label>
                            <select id="saved_address" class="form-group">
                                <option value="">-- Chọn một địa chỉ --</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" 
                                            data-province-id="{{ $address->province_id }}"
                                            data-district-id="{{ $address->district_id }}"
                                            data-ward-id="{{ $address->ward_id }}"
                                            data-address-detail="{{ $address->address_detail }}">
                                        {{ $address->address_detail }}, {{ optional($address->ward)->name }}, {{ optional($address->district)->name }}, {{ optional($address->province)->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="province">Tỉnh/Thành phố</label>
                            <select id="province" name="province_id" class="form-group" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p['ProvinceID'] }}">
                                        {{ $p['ProvinceName'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="form-group">
                            <label for="district">Quận/Huyện</label>
                            <select id="district" name="district_id" class="form-group" required>
                               <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                
                        <div class="form-group">
                            <label for="ward">Xã/Phường</label>
                            <select id="ward" name="ward_id" class="form-group" required>
                                <option value="">Chọn Xã/Phường</option>
                            </select>
                        </div>
                
                        <div class="form-group">
                            <label for="address_detail">Địa chỉ chi tiết (Số nhà, tên đường...)</label>
                            <input type="text" name="address_detail" id="address_detail" class="form-group" placeholder="Nhập địa chỉ chi tiết" required>
                        </div>

                        {{-- Nút submit sẽ được chuyển sang cột tóm tắt đơn hàng --}}
                    </form>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="order-summary">
                <h3>Tóm tắt đơn hàng</h3>

                @if (isset($cartDetails) && count($cartDetails) > 0)
                    <div class="table-responsive" style="margin-bottom: 20px;">
                        <table style="font-size: 0.9rem;">
                            <tbody>
                                @foreach ($cartDetails as $item)
                                    <tr>
                                        <td style="padding: 10px 5px;">
                                            {{ $item->productVariant->product->name }} 
                                            <small class="d-block text-muted">
                                                @foreach ($item->productVariant->attributeValues as $value) {{ $value->value }}@if(!$loop->last), @endif @endforeach
                                                x {{ $item->quantity }}
                                            </small>
                                        </td>
                                        <td style="text-align: right; padding: 10px 5px; font-weight: 600;">{{ number_format($item->amount) }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span class="summary-total-label">Tổng tiền:</span>
                        <span class="summary-total-amount">{{ number_format($total) }}đ</span>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label for="payment_method">Phương thức thanh toán</label>
                        <select name="payment_id" id="payment_method" class="form-group" form="checkout-form" required>
                            <option value="1">COD (Thanh toán khi nhận hàng)</option>
                            <option value="2">MoMo</option>
                            <option value="3">VNPay</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="note">Ghi chú (tùy chọn)</label>
                        <input type="text" name="note" id="note" class="form-group" placeholder="Ghi chú cho người bán..." form="checkout-form">
                    </div>

                    <div
                        style="background-color: #f0f9ff; padding: 12px; border-radius: 8px; font-size: 0.85rem; color: #0369a1; text-align: center; border: 1px solid #bae6fd;">
                        ✓ Miễn phí giao hàng cho đơn hàng từ 500.000đ
                    </div>
                @else
                    <p style="text-align: center; color: #718096; padding: 20px;">Giỏ hàng của bạn trống</p>
                @endif

                <div class="buttons-section">
                    <button type="submit" class="btn btn-primary" form="checkout-form">Hoàn tất đơn hàng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const savedAddressSelect = document.getElementById('saved_address');
    const addressDetailInput = document.getElementById('address_detail');

    // Hàm gọi API để lấy danh sách Quận/Huyện
    function loadDistricts(provinceId, selectedDistrictId = null) {
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
        if (!provinceId) {
            wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            return;
        }

        fetch(`{{ route('client.checkout.districts') }}?province_id=${provinceId}`)
            .then(res => res.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                const districts = Array.isArray(data) ? data : (data.data || []);
                districts.forEach(d => {
                    const option = new Option(d.DistrictName, d.DistrictID);
                    if (selectedDistrictId && d.DistrictID == selectedDistrictId) {
                        option.selected = true;
                    }
                    districtSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Lỗi khi tải danh sách quận/huyện:', err);
                districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    }

    // Hàm gọi API để lấy danh sách Phường/Xã
    function loadWards(districtId, selectedWardId = null) {
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        if (!districtId) {
            wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
            return;
        }

        fetch(`{{ route('client.checkout.wards') }}?district_id=${districtId}`)
            .then(res => res.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
                const wards = Array.isArray(data) ? data : (data.data || []);
                wards.forEach(w => {
                    const option = new Option(w.WardName, w.WardCode);
                    if (selectedWardId && w.WardCode == selectedWardId) {
                        option.selected = true;
                    }
                    wardSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Lỗi khi tải danh sách phường/xã:', err);
                wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    }

    provinceSelect.addEventListener('change', e => loadDistricts(e.target.value));
    districtSelect.addEventListener('change', e => loadWards(e.target.value));

    // Xử lý khi chọn địa chỉ đã lưu
    if (savedAddressSelect) {
        savedAddressSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) return;

            const provinceId = selectedOption.dataset.provinceId;
            const districtId = selectedOption.dataset.districtId;
            const wardId = selectedOption.dataset.wardId;
            const addressDetail = selectedOption.dataset.addressDetail;

            provinceSelect.value = provinceId;
            addressDetailInput.value = addressDetail;
            
            // Tải danh sách quận/huyện và tự động chọn đúng quận
            loadDistricts(provinceId, districtId);

            // Tải danh sách phường/xã và tự động chọn đúng xã
            loadWards(districtId, wardId);
        });
    }
});
</script>
@endsection