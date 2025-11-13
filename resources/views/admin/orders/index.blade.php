@extends('layouts.admin.admin')
@section('title', 'Đơn Hàng')
@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-dark">Danh Sách Đơn Hàng</h1>
        </div>

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
                        <th>Trạng Thái</th>
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
                                        <li>{{ $detail->productVariant->product->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $order->booking_date }}</td>
                            <td>{{ number_format($order->total, 0, ',', '.') }}₫</td>
                            <td>
                                <select name="status_id" id="status_id">
                                    @foreach ($status as $item)
                                        <option value="{{ $item->status_id }}" {{ $order->status_id == $item->id ? 'selected' : ''}}>
                                            {{ $item->status_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Chi tiết</a>
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
@endsection