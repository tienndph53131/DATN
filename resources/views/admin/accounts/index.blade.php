@extends('layouts.admin.admin')

@section('content')
    @php
        $currentUser = auth()->guard('client')->user();
    @endphp
    <div class="container mt-4">
        <h2 class="mb-3">Quản lý tài khoản</h2>

        <!-- Form tìm kiếm -->
        <form method="GET" action="{{ route('accounts.index') }}" class="mb-3 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm theo tên hoặc email..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </form>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr class="text-center">
                <th>ID</th>
                <th>Ảnh đại diện</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Giới tính</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        @forelse($accounts as $account)
            <tr>
                <td class="text-center">{{ $account->id }}</td>
                <td class="text-center">
                    @if($account->avatar)
                        <img src="{{ asset($account->avatar) }}" alt="avatar" width="60" height="60" class="rounded-circle">
                    @else
                        <img src="https://via.placeholder.com/60" alt="avatar" class="rounded-circle">
                    @endif
                </td>
                <td>{{ $account->name }}</td>
                <td>{{ $account->email }}</td>
                <td>{{ $account->phone ?? '—' }}</td>
                <td>
                    @if($account->sex === 'male') Nam
                    @elseif($account->sex === 'female') Nữ
                    @else Khác
                    @endif
                </td>
                <td>{{ $account->role->name ?? 'Chưa có' }}</td>
                <td>
                    <span class="badge bg-{{ $account->status ? 'success' : 'secondary' }}">
                        {{ $account->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="text-center">
                    <a href="{{ route('accounts.show', $account->id) }}" class="btn btn-sm btn-info">Xem</a>
                    <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                   
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted">Không có tài khoản nào.</td></tr>
        @endforelse
        </tbody>
    </table>

        <div class="d-flex justify-content-center">
            {{ $accounts->links() }}
        </div>
    </div>
@endsection