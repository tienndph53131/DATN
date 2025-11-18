@extends('layouts.admin.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Chi tiết đơn hàng {{ $order->order_code }}</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
            <strong>Khách hàng:</strong> {{ optional($order->account)->name ?? 'Khách' }}<br>
            <strong>Email:</strong> {{ $order->email }}<br>
            <strong>Điện thoại:</strong> {{ $order->phone }}<br>
            <strong>Địa chỉ:</strong> {{ optional($order->address)->address_detail ?? '—' }}
        </div>

        <h5>Chi tiết sản phẩm</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>SL</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->details as $d)
                    <tr>
                        <td>{{ optional($d->productVariant->product)->name ?? 'Sản phẩm' }}</td>
                        <td>{{ $d->quantity }}</td>
                        <td>{{ number_format($d->price,0,',','.') }}₫</td>
                        <td>{{ number_format($d->amount,0,',','.') }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <strong>Tổng:</strong> {{ number_format($order->total,0,',','.') }}₫
        </div>

        <hr>

        <form action="{{ route('orders.update', $order->id) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label">Trạng thái</label>
                    <select name="status_id" class="form-select">
                        <option value="">Chọn trạng thái</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}" {{ $order->status_id == $s->id ? 'selected' : '' }}>{{ $s->status_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success mt-4">Cập nhật trạng thái</button>
                </div>
            </div>
        </form>

        <hr>
        <h5 class="mt-3">Lịch sử thay đổi trạng thái</h5>
        @if(isset($logs) && $logs->count())
            <table class="table table-sm table-bordered mt-2">
                <thead class="table-light">
                    <tr>
                        <th>Thời gian</th>
                        <th>Trạng thái cũ</th>
                        <th>Trạng thái mới</th>
                        <th>Người thực hiện</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->oldStatus->status_name ?? '—' }}</td>
                            <td>{{ $log->newStatus->status_name ?? '—' }}</td>
                            <td>{{ $log->user ? $log->user->name : 'Hệ thống' }}</td>
                            <td>{{ $log->note ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">Chưa có thay đổi trạng thái nào.</p>
        @endif

    </div>
</div>
@endsection
