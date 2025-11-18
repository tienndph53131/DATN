@extends('layouts.admin.admin')

@section('title', 'Đơn hàng')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Danh sách đơn hàng</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Khách hàng</th>
                    <th>Tổng</th>
                    <th>Trạng thái</th>
                    <th>Ngày</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ optional($order->account)->name ?? 'Khách' }}</td>
                        <td>{{ number_format($order->total, 0, ',', '.') }}₫</td>
                        <td>{{ optional($order->status)->status_name ?? '—' }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">Xem</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
</div>
@endsection
