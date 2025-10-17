<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f5f7;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100%;
            background: #1f2937;
            color: #fff;
            padding-top: 20px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #cbd5e1;
            text-decoration: none;
            margin: 5px 10px;
            border-radius: 5px;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #4b5563;
            color: #fff;
        }
        .sidebar i { margin-right: 10px; width: 20px; text-align: center; }

        .main {
            margin-left: 220px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            padding: 20px;
            flex-grow: 1;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center mb-4"><i class="fa-solid fa-user-shield"></i> ADMIN</h4>
    <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a href="{{ url('/admin/categories') }}" class="{{ request()->is('admin/categories*') ? 'active' : '' }}"><i class="fa-solid fa-layer-group"></i> Danh mục</a>
    <a href="{{ url('/admin/products') }}" class="{{ request()->is('admin/products*') ? 'active' : '' }}"><i class="fa-solid fa-shirt"></i> Sản phẩm</a>
    <a href="{{ url('/admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i> Đơn hàng</a>
    <a href="{{ url('/admin/attributes') }}" class="{{ request()->is('admin/attributes*') ? 'active' : '' }}"><i class="fa-solid fa-xmark"></i> Thuộc tính</a>
    <a href="{{ url('/admin/users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Người dùng</a>
    <a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
</div>

<div class="main">
    @include('layouts.admin.header')  {{-- Header --}}
    <div class="content">
        @yield('content')
    </div>
    @include('layouts.admin.footer')  {{-- Footer --}}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
