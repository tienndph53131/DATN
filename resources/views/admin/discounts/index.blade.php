@extends('layouts.admin.admin')

@section('content')

    <form method="GET" action="{{ route('discounts.index') }}" class="mb-3 d-flex gap-2">
        <!-- Tìm theo tên -->
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên" class="form-control">

        <!-- Chọn ma giam gia -->
        <select name="discount_type" class="form-select">
            <option value="">-- Chọn ma giam gia --</option>
            <option value="percent" {{ request('discount_type' == 'percent' ? 'selected' : '') }}>Phần trăm</option>
            <option value="fixed" {{ request('discount_type' == 'fixed' ? 'selected' : '') }}>Số tiền</option>
        </select>

        <!-- Chọn trạng thái (1 = còn, 0 = hết) -->
        <select name="active" class="form-select">
            <option value="">-- Chọn trạng thái --</option>
            <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Tắt </option>
            <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Kích hoạt</option>
        </select>
        <button class="btn btn-primary">Tìm kiếm</button>
    </form>
    <div class="container mt-4">
        <h2>Danh sách mã giảm giá</h2>
        <a href="{{ route('discounts.create') }}" class="btn btn-primary mb-3">+ Thêm mã giảm giá</a>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã</th>
<th>Mô tả</th>
<th>Loại giảm giá</th>
<th>Giá trị giảm</th>
<th>Ngày bắt đầu</th>
<th>Ngày kết thúc</th>
<th>Trạng thái</th>
<th>Giá trị tối thiểu</th>
<th>Giới hạn sử dụng</th>
<th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discounts as $discount)
                    <tr>
                        <td>{{ $discount->code }}</td>
                        <td>{{ $discount->description }}</td>
                        <td>{{ $discount->discount_type}}</td>
                        <td>
                            @if ($discount->discount_type == 'percent')
                                {{ number_format($discount->discount_value, '0', ',', '.')}}%
                            @else
                                {{ number_format($discount->discount_value, '0', ',', '.')}} VND
                            @endif
                        </td>
                        <td>{{ $discount->start_date }}</td>
                        <td>{{ $discount->end_date }}</td>
                        <td>
                            @if ($discount->active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secodary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ number_format($discount->minimum_order_amount, 0, ',', '.') }} VND</td>
                        <td>{{ $discount->usage_limit }}</td>
                        <td>
                            <a href="{{ route('discounts.edit', $discount->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Xóa mã này?')" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection