@extends('layouts.admin.admin')

@section('content')
    <div class="container mt-4">
        <h2>sửa mã giảm giá mới</h2>
        <form action="{{ route('discounts.update', $discount->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Mã giảm giá</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                    value="{{ $discount->code }}">
                     @error('code')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
            </div>
            <div class="mb-3">
                <label>Mô tả</label>
                <textarea name="description" class="form-control">{{ $discount->description }}</textarea>
            </div>
            <div class="mb-3">
                <label>loại giảm giá</label>
                <select name="discount_type" class="form-control @error('discount_type') is-invalid @enderror"
                    value="{{ old('discount_type') }}">
                    <option value="percent" @selected($discount->percent)>Phần trăm</option>
                    <option value="fixed" @selected($discount->fixed)>Số tiền</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Số giảm giá</label>
                <input type="number" name="discount_value"
                    class="form-control @error('discount_value') is-invalid @enderror"
                    value="{{ $discount->discount_value }}">
            </div>
            <div class="mb-3">
                <label>Thời gian bắt đầu</label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                    value="{{ $discount->start_date }}">
            </div>
            <div class="mb-3">
                <label>Thời gian kết thúc</label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                    value="{{ $discount->end_date }}">
            </div>
            <div class="mb-3">
                <label>Trạng thái</label>
                <select name="active" id="active">
                    <option value="1" @selected($discount->active == 1)>Kích hoạt</option>
                    <option value="0" @selected($discount->active == 0)>Tắt</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Số tiền tối thiểu</label>
                <input type="number" name="minimum_order_amount"
                    class="form-control @error('minimum_order_amount') is-invalid @enderror"
                    value="{{$discount->minimum_order_amount }}">
            </div>
            <div class="mb-3">
                <label>Số lượng sửa dụng</label>
                <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror"
                    value="{{ $discount->usage_limit }}">
            </div>
            <button class="btn btn-primary">Lưu</button>
            <a href="{{ route('discounts.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
@endsection