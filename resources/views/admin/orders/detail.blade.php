@extends('layouts.admin.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-4">

    <h3 class="fw-bold mb-4">Chi tiết đơn hàng: {{ $order->order_code }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- HIỂN THỊ LỖI TỪ CONTROLLER --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            **Lỗi cập nhật:**
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4 border p-3 rounded bg-light">
            <h5>Thông tin khách hàng</h5>
            <div class="row mb-2">
                <div class="col-md-4">
                    <label class="form-label">Người nhận</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $order->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">SĐT</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $order->phone) }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">email</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $order->email ?? '') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Accuont</label>
                     <input type="text" class="form-control" value="{{ $order->account->name ?? 'Khách lạ' }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phương thức thanh toán</label>
                    <input type="text" class="form-control" value="{{ $order->payment->payment_method_name ?? '---' }}" disabled>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Địa chỉ giao hàng</label>
                <input type="text" name="address_detail" class="form-control @error('address_detail') is-invalid @enderror" value="{{ old('address_detail', $order->ghn_address['address_detail'] ?? '') }}">
                @error('address_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          <div class="mb-2">
                <label class="form-label">Tỉnh / Thành phố</label>
                <input type="text" name="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $order->ghn_address['province'] ?? '') }}" disabled>
                @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-2">
                <label class="form-label">Quận / Huyện</label>
                <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" value="{{ old('district', $order->ghn_address['district'] ?? '') }}" disabled>
                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-2">
                <label class="form-label">Phường / Xã</label>
                <input type="text" name="ward" class="form-control @error('ward') is-invalid @enderror" value="{{ old('ward', $order->ghn_address['ward'] ?? '') }}" disabled>
                @error('ward')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-4 border p-3 rounded bg-light">
            <h5>Trạng thái</h5>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Trạng thái đơn hàng</label>
                    <select name="status_id" class="form-select @error('status_id') is-invalid @enderror">
                        
                        
                        @php
                            $currentStatusId = $order->status_id;
                            // Trạng thái kết thúc: 5 (Thành công), 6 (Hoàn hàng), 7 (Hủy đơn hàng)
                            $terminalStatuses = [5, 6, 7];
                            // CÁC ID KIỂM TRA MỚI
                            $ORDER_SHIPPING_ID = 3; // ID "Đang giao"
                            $ORDER_RECEIVED_ID = 4; // ID "Đã giao" 
                            $ORDER_RETURN_ID = 6;   // ID "Hoàn hàng"
                            $ORDER_CANCEL_ID = 7;   // ID "Hủy đơn hàng"
                        @endphp
                        
                        @foreach($status as $s)
                            @php
                                $isDisabled = false;
                                
                                //  Ngăn chặn quay ngược trạng thái (Trừ ID 7 - Hủy)
                                if ($s->id < $currentStatusId && $s->id != 7) {
                                    $isDisabled = true;
                                }

                                //  Nếu đơn hàng đã đạt trạng thái Kết thúc, chỉ cho phép chọn lại chính trạng thái đó
                                if (in_array($currentStatusId, $terminalStatuses) && $s->id != $currentStatusId) {
                                    $isDisabled = true;
                                }

                                //  CHẶN HOÀN HÀNG (ID 6) NẾU CHƯA ĐẠT ID 4 ("Đã giao")
                                // Nếu trạng thái là Hoàn hàng VÀ trạng thái hiện tại nhỏ hơn ID Đã giao (5)
                                if ($s->id == $ORDER_RETURN_ID && $currentStatusId < $ORDER_RECEIVED_ID) {
                                    $isDisabled = true;
                                }
                                // CHẶN HỦY ĐƠN HÀNG (ID 7) NẾU ĐƠN HÀNG ĐÃ GỬI VẬN CHUYỂN (ID 3)
                              
                                if ($s->id == $ORDER_CANCEL_ID && $currentStatusId >= $ORDER_SHIPPING_ID) {
                                    $isDisabled = true;
                                }
                            @endphp
                            
                            <option 
                                value="{{ $s->id }}" 
                                {{ old('status_id', $currentStatusId) == $s->id ? 'selected' : '' }}
                                {{ $isDisabled ? 'disabled' : '' }} {{-- THÊM THUỘC TÍNH DISABLED --}}
                            >
                                {{ $s->status_name }}
                                {{ $isDisabled ? ' ' : '' }} 
                            </option>
                        @endforeach
                    </select>
                    @error('status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                {{-- TRẠNG THÁI THANH TOÁN --}}
                <div class="col-md-6 mb-2">
                    <label class="form-label">Trạng thái thanh toán</label>
                    @php
                        $paymentStatus = $order->paymentStatus->status_name ?? '---';
                        $paymentClass = match($paymentStatus) {
                            'Chưa thanh toán' => 'badge bg-warning text-dark',
                            'Đã thanh toán' => 'badge bg-success',
                            default => 'badge bg-light text-dark',
                        };
                    @endphp
                    <div class="pt-2"><span class="{{ $paymentClass }}">{{ $paymentStatus }}</span></div>
                </div>
                
                
            </div>
        </div>

        <div class="mb-4">
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
                                {{-- Ảnh sản phẩm --}}
                                 @if($detail->product && $detail->product->image)
                                <img src="{{ asset('uploads/products/' . $detail->product->image) }}"
                                    class="rounded border mt-2"
                                        style="width: 70px; height: auto;">
                                       @endif
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
             @if ($order->discount_amount > 0)
        <p class="fs-5 text-success">
            <strong>Giảm giá:</strong> -{{ number_format($order->discount_amount) }} đ
        </p>
    @endif
            <div class="text-end">
                <h4 class="fw-bold">Tổng tiền: {{ number_format($order->total) }} đ</h4>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Cập nhật thông tin & trạng thái</button>
    </form>
</div>
@endsection