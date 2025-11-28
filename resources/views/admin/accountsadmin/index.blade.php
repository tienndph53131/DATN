@extends('layouts.admin.admin')

@section('title', 'Quản lý tài khoản')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Danh sách tài khoản</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(Auth::guard('client')->user()->role_id == 1)
        <a href="{{ route('accountsadmin.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Role</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
                <tr>
                    <td>{{ $account->id }}</td>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->email }}</td>
                    <td>{{ $account->status ? 'Hoạt động' : 'Vô hiệu' }}</td>
                    <td>
                        @if($account->role_id == 1)
                            Admin
                        @elseif($account->role_id == 3)
                            Nhân viên
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('accountsadmin.show', $account->id) }}" class="btn btn-info btn-sm">Xem</a>

                        @if(Auth::guard('client')->user()->role_id == 1)
                            <a href="{{ route('accountsadmin.edit', $account->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                           
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
