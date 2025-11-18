@extends('layouts.admin.admin')

@section('title', 'Thêm trạng thái đơn hàng')
@section('content')
    <div class="container-fluid mt-4">
        <h1 class="h3 mb-4">Thêm trạng thái đơn hàng</h1>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('order-statuses.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Tên trạng thái</label>
                <input type="text" name="status_name" class="form-control" value="{{ old('status_name') }}" required>
            </div>
            <button class="btn btn-primary">Lưu</button>
            <a href="{{ route('order-statuses.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
@endsection
