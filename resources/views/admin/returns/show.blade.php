@extends('layouts.admin.admin')

@section('title', 'Yêu cầu hoàn hàng (Đã xoá)')
@section('content')
    <div class="container-fluid mt-4">
        <h1 class="h3 mb-4">Yêu cầu hoàn hàng đã bị xoá</h1>
        <p>Chi tiết yêu cầu không còn hiển thị vì tính năng đã bị gỡ.</p>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại danh sách đơn</a>
    </div>
@endsection
