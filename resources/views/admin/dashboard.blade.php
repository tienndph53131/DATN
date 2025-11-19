<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@extends('layouts.admin.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="py-4">
            <h1>Welcome to Admin Dashboard</h1>
            <p>Bạn đã đăng nhập với quyền quản trị viên.</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Quản lý danh mục</h5>
                        <p class="card-text">Truy cập nhanh vào danh mục sản phẩm.</p>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">Danh mục</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Quản lý sản phẩm</h5>
                        <p class="card-text">Thêm / chỉnh sửa sản phẩm.</p>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Sản phẩm</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Quản lý tài khoản</h5>
                        <p class="card-text">Thêm / chỉnh sửa tài khoản người dùng.</p>
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary">Tài khoản</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
</body>
</html> -->