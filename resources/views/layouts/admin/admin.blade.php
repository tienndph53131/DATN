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
<a href="{{ url('/admin/attribute_values') }}" class="{{ request()->is('admin/attribute_values*') ? 'active' : '' }}">
    <i class="fa-solid fa-tag"></i> Giá trị thuộc tính
</a>
<a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Người dùng</a>
{{-- @if(Auth::guard('client')->check() && in_array(Auth::guard('client')->user()->role_id, [1,3])) --}}
    <a href="{{ route('accountsadmin.index') }}" 
       class="{{ request()->routeIs('accountsadmin.*') ? 'active' : '' }}">
       <i class="fa-solid fa-users"></i> Admin / Nhân viên
    </a>
{{-- @endif    --}}
     <li><a class="dropdown-item" href="{{ route('comments.index') }}"><i class="fa-solid fa-comments me-2"></i> Quản lý bình luận</a></li>
            <li><hr class="dropdown-divider"></li>
    <a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
</div>

<div class="main">
    @include('layouts.admin.header')  {{-- Header --}}
    <div class="content">
        @yield('content')
    </div>
    @include('layouts.admin.footer')  {{-- Footer --}}
</div>
<!-- Toast container for admin notifications -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
    <div id="globalToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="globalToastBody">&nbsp;</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
@yield('scripts')
</body>
</html>
<!-- Confirm modal used by admin pages for destructive actions -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Xác nhận</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">Bạn có chắc chắn muốn thực hiện hành động này?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="confirmModalYes" class="btn btn-danger">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
