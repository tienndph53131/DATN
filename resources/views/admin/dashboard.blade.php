@extends('layouts.admin.admin')
@section('title', 'Dashboard')
@section('content')

<div class="container py-4">
    <h1 class="mb-4">Dashboard</h1>

    <!-- Bộ lọc thời gian -->
    <form method="get" class="mb-4 d-flex gap-2 align-items-center">
        <label>Start:</label>
        <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-control form-control-sm">
        <label>End:</label>
        <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-control form-control-sm">
        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
    </form>

    <!-- Card tổng doanh thu -->
    <div class="card mb-4 p-3">
        <h4>Tổng doanh thu</h4>
        <p class="fs-3">{{ number_format($totalRevenue) }} VNĐ</p>
    </div>

    <!-- Biểu đồ doanh thu theo ngày -->
    <div class="card mb-4 p-3">
        <h4>Doanh thu theo ngày</h4>
        <canvas id="revenueChart"></canvas>
    </div>

    <div class="row">
        <!-- Top 10 user -->
        <div class="col-md-6">
            <div class="card p-3 mb-4">
                <h4>Top 10 user đặt hàng nhiều nhất</h4>
                <table class="table table-sm">
                    <tr><th>User</th><th>Đơn hàng</th><th>Tổng chi</th></tr>
                    @foreach($topUsers as $u)
                        <tr>
                            <td>{{ $u->account->name }}</td>
                            <td>{{ $u->order_count }}</td>
                            <td>{{ number_format($u->total_spent) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <!-- Top 10 sản phẩm bán chạy -->
        <div class="col-md-6">
            <div class="card p-3 mb-4">
                <h4>Top 10 sản phẩm bán chạy</h4>
                <table class="table table-sm">
                    <tr><th>Sản phẩm</th><th>Đã bán</th></tr>
                    @foreach($topProducts as $p)
                        <tr>
                            <td>{{ $p->product->name }}</td>
                            <td>{{ $p->sold_qty }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <!-- Top 10 sản phẩm ế -->
            <div class="card p-3 mb-4">
                <h4>Top 10 sản phẩm ế nhất</h4>
                <table class="table table-sm">
                    <tr><th>Sản phẩm</th><th>Đã bán</th></tr>
                    @foreach($unsoldProducts as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->sold_qty }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueByDay->pluck('date')) !!},
        datasets: [{
            label: 'Doanh thu',
            data: {!! json_encode($revenueByDay->pluck('revenue')) !!},
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endsection
