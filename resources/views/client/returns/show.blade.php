@extends('layouts.app')
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Chi tiết yêu cầu (Đã xoá)</h2>
    <p>Chi tiết yêu cầu không khả dụng vì tính năng đã bị gỡ.</p>
    <a href="{{ url('/') }}" class="btn btn-secondary">Về trang chủ</a>
</div>
@endsection
            @if($rr->photos && is_array($rr->photos))
