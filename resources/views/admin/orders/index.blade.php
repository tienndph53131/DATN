@extends('layouts.admin.admin')

@section('title', 'Đơn Hàng')
@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-dark">Danh Sách Đơn Hàng</h1>
        </div>

        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" placeholder="Tìm kiếm theo mã đơn, khách hàng..." id="searchInput">
            </div>
            <div class="col-md-4 mb-2">
                <select id="statusFilter" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="ordersTable">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái đơn hàng</th>
                        <th>Trạng Thái Thanh Toán</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr data-status-id="{{ $order->status_id }}">
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->account->name ?? 'Khách lạ' }}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach($order->details as $detail)
                                       {{ $detail->productVariant?->product?->name ?? 'Không tìm thấy sản phẩm' }}
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $order->booking_date }}</td>
                            <td>{{ number_format($order->total, 0, ',', '.') }}₫</td>
                           <td>
    @php
        $status = $order->status->status_name ?? 'Chưa xác định';
        $statusClass = match($status) {
            'Chưa xác nhận' => 'badge bg-secondary',
            'Đã thanh toán, chờ xác nhận' => 'badge bg-primary',
            'Đã xác nhận' => 'badge bg-primary',
            'Đang chuẩn bị hàng' => 'badge bg-info text-dark',
            'Đang giao' => 'badge bg-warning text-dark',
            'Đã giao' => 'badge bg-success',
            'Đã nhận' => 'badge bg-success',
            'Thành công' => 'badge bg-success',
            'Hoàn hàng' => 'badge bg-danger',
            'Hủy đơn hàng' => 'badge bg-dark',
            default => 'badge bg-light text-dark',
        };
    @endphp
    <span class="{{ $statusClass }}">{{ $status }}</span>
</td>

               <td>
                        <span class="badge 
                            {{ $order->paymentStatus && $order->paymentStatus->id == 2 ? 'bg-success' : 'bg-warning' }}">
                            {{ $order->paymentStatus->status_name ?? '---' }}
                        </span>
                    </td>
                            <td>
                                <div class="d-flex">
                                    <form method="POST" action="{{ route('orders.update', $order->id) }}" class="d-flex me-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status_id" class="form-select form-select-sm me-2">
                                            <option value="">Chọn trạng thái</option>
                                            @foreach($statuses as $s)
                                                <option value="{{ $s->id }}" {{ $order->status_id == $s->id ? 'selected' : '' }}>{{ $s->status_name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-primary">Cập nhật</button>
                                    </form>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none'
            })
        })
    </script>
@endsection
