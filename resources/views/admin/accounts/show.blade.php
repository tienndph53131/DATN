@extends('layouts.admin.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Thông tin tài khoản</h2>

    <div class="card shadow-sm p-4">
        <div class="text-center mb-3">
            @if($account->avatar)
                <img src="{{ asset($account->avatar) }}" alt="Avatar" class="rounded-circle" width="120" height="120">
            @else
                <img src="https://via.placeholder.com/120" class="rounded-circle" alt="Avatar">
            @endif
        </div>

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
                <th>Điện thoại</th>
                <td>{{ $account->phone ?? '—' }}</td>
            </tr>
            <tr>
                <th>Giới tính</th>
                <td>
                    @if($account->sex === 'male') Nam
                    @elseif($account->sex === 'female') Nữ
                    @else Khác
                    @endif
                </td>
            </tr>
            <tr>
                <th>Ngày sinh</th>
                <td>{{ $account->birthday ? \Carbon\Carbon::parse($account->birthday)->format('d/m/Y') : '—' }}</td>
            </tr>
            <tr>
                <th>Vai trò</th>
                <td>{{ $account->role->name ?? 'Chưa có' }}</td>
            </tr>
            <tr>
                <th>Trạng thái</th>
                <td>
                    <span class="badge bg-{{ $account->status ? 'success' : 'secondary' }}">
                        {{ $account->status ? 'Hoạt động' : 'Ngừng hoạt động' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Ngày tạo</th>
                <td>{{ $account->created_at ? $account->created_at->format('d/m/Y H:i') : '—' }}</td>
            </tr>
            <tr>
                <th>Ngày cập nhật</th>
                <td>{{ $account->updated_at ? $account->updated_at->format('d/m/Y H:i') : '—' }}</td>
            </tr>
        </table>

        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
        </div>
    </div>
</div>
@endsection
