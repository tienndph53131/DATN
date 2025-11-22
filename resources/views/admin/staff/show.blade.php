@extends('layouts.admin.admin')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Thông tin tài khoản nhân viên</h2>

        <div class="card shadow-sm p-4">
            <div class="text-center mb-3">
                @if($staff->avatar)
                    <img src="{{ asset($staff->avatar) }}" alt="Avatar" class="rounded-circle" width="120" height="120">
                @else
                    <img src="https://via.placeholder.com/120" class="rounded-circle" alt="Avatar">
                @endif
            </div>

            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td>{{ $staff->id }}</td>
                </tr>
                <tr>
                    <th>Họ tên</th>
                    <td>{{ $staff->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $staff->email }}</td>
                </tr>
                <tr>
                    <th>Điện thoại</th>
                    <td>{{ $staff->phone ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Giới tính</th>
                    <td>
                        @if($staff->sex === 'male') Nam
                        @elseif($staff->sex === 'female') Nữ
                        @else Khác
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Ngày sinh</th>
                    <td>{{ $staff->birthday ? \Carbon\Carbon::parse($staff->birthday)->format('d/m/Y') : '—' }}</td>
                </tr>
                <tr>
                    <th>Vai trò</th>
                    <td>{{ $staff->role->name ?? 'Chưa có' }}</td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        <span class="badge bg-{{ $staff->status ? 'success' : 'secondary' }}">
                            {{ $staff->status ? 'Hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $staff->created_at ? $staff->created_at->format('d/m/Y H:i') : '—' }}</td>
                </tr>
                <tr>
                    <th>Ngày cập nhật</th>
                    <td>{{ $staff->updated_at ? $staff->updated_at->format('d/m/Y H:i') : '—' }}</td>
                </tr>
            </table>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-warning">Sửa</a>
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
            </div>
        </div>
    </div>
@endsection