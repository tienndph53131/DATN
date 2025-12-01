@extends('layouts.admin.admin')

@section('content')
    <div class="container mt-4">
        <h2>Thêm ma giam gia mới</h2>
        <form action="{{ route('discounts.update', $discount->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Ma giam gia</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                    value="{{ $discount->code }}">
            </div>
            <div class="mb-3">
                <label>Mô tả</label>
                <textarea name="description" class="form-control">{{ $discount->description }}</textarea>
            </div>
            <div class="mb-3">
                <label>Loai giam gia</label>
                <select name="discount_type" class="form-control @error('discount_type') is-invalid @enderror"
                    value="{{ old('discount_type') }}">
                    <option value="percent" @selected($discount->percent)>phan tram</option>
                    <option value="fixed" @selected($discount->fixed)>So tien</option>
                </select>
            </div>
            <div class="mb-3">
                <label>So giam gia</label>
                <input type="number" name="discount_value"
                    class="form-control @error('discount_value') is-invalid @enderror"
                    value="{{ $discount->discount_value }}">
            </div>
            <div class="mb-3">
                <label>Thoi gian bat dau </label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                    value="{{ $discount->start_date }}">
            </div>
            <div class="mb-3">
                <label>Thoi gian ket thuc</label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                    value="{{ $discount->end_date }}">
            </div>
            <div class="mb-3">
                <label>Active</label>
                <select name="active" id="active">
                    <option value="1" @selected($discount->active == 1)>Kich hoat</option>
                    <option value="0" @selected($discount->active == 0)>Khong kich hoat</option>
                </select>
            </div>
            <div class="mb-3">
                <label>So tien toi thieu</label>
                <input type="number" name="minimum_order_amount"
                    class="form-control @error('minimum_order_amount') is-invalid @enderror"
                    value="{{$discount->minimum_order_amount }}">
            </div>
            <div class="mb-3">
                <label>So luong su dung</label>
                <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror"
                    value="{{ $discount->usage_limit }}">
            </div>
            <button class="btn btn-primary">Lưu</button>
            <a href="{{ route('discounts.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
@endsection