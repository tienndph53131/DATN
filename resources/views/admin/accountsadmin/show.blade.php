@extends('layouts.admin.admin')

@section('title', 'Chi tiết tài khoản')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Chi tiết tài khoản</h3>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $account->id }}</td>
        </tr>
        <tr>
            <th>Họ tên</th>
            <td>{{ $account->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $account->email }}</td>
        </tr>
        <tr>
            <th>Role</th>
            <td>{{ $account->role_id == 1 ? 'Admin' : 'Nhân viên' }}</td>
        </tr>
        <tr>
            <th>Trạng thái</th>
            <td>{{ $account->status ? 'Hoạt động' : 'Vô hiệu' }}</td>
        </tr>
    </table>

    <a href="{{ route('accountsadmin.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
