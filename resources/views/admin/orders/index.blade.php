@extends('layouts.admin.admin')
@section('title', 'Đơn Hàng')
@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-dark">Danh Sách Đơn Hàng</h1>
        </div>
        <form action="{{ route('orders.index') }}" method="GET" class="mb-4">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label for="status_id" class="form-label">Lọc theo trạng thái:</label>
                <select name="status_id" id="status_id" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    {{-- Lặp qua danh sách trạng thái để tạo options --}}
                    @foreach($status as $s)
                        <option 
                            value="{{ $s->id }}" 
                            {{-- Giữ lại lựa chọn hiện tại sau khi lọc --}}
                            {{ $filterStatusId == $s->id ? 'selected' : '' }}
                        >
                            {{ $s->status_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Đặt lại</a>
            </div>
        </div>
    </form>

        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <input type="text" class="form-control" placeholder="Tìm kiếm theo mã đơn, khách hàng..." id="searchInput">
            </div>
            {{-- <div class="col-md-3 mb-2">
                <select class="form-select" id="statusFilter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1">Đang xử lý</option>
                    <option value="2">Đã hoàn thành</option>
                    <option value="3">Bị hủy</option>
                </select>
            </div> --}}
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
                        <tr>
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
                               
                               <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">Thao tác</a>
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
                const text = row.textContent.toLowerCase();// lay text
                row.style.display = text.includes(filter) ? '' : 'none' // hien thi neu co filter
            })
        })
    </script>

<div class="d-flex justify-content-center mt-4">
    <nav aria-label="Order Pagination">
        {{ $orders->links('pagination::bootstrap-5') }} 
    </nav>
</div>
@endsection