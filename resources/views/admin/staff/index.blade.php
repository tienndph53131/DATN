@extends('layouts.admin.admin')

@section('content')
    @php
        $currentUser = auth()->guard('client')->user();
    @endphp
    <div class="container mt-4">
        <h2 class="mb-3">Quản lý tài khoản nhân viên</h2>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">Thêm nhân viên</a>
        <!-- Form tìm kiếm -->
        <form method="GET" action="{{ route('staff.index') }}" class="mb-3 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm theo tên hoặc email..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </form>
        <a href="{{  route('staff.index')}}" class="btn btn-secondary">Hủy</a>

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
                @forelse($staffs as $staff)
                    <tr>
                        <td class="text-center">{{ $staff->id }}</td>
                        <td class="text-center">
                            @if($staff->avatar)
                                <img src="{{ asset($staff->avatar) }}" alt="avatar" width="60" height="60" class="rounded-circle">
                            @else
                                <img src="https://via.placeholder.com/60" alt="avatar" class="rounded-circle">
                            @endif
                        </td>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>{{ $staff->phone ?? '—' }}</td>
                        <td>
                            @if($staff->sex === 'male') Nam
                            @elseif($staff->sex === 'female') Nữ
                            @else Khác
                            @endif
                        </td>
                        <td>{{ $staff->role->name ?? 'Chưa có' }}</td>
                        <td>
                            <span class="badge bg-{{ $staff->status ? 'success' : 'secondary' }}">
                                {{ $staff->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-sm btn-info">Xem</a>
                            <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                            <form action="{{ route('staff.destroy', $staff->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Không có tài khoản nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $staffs->links() }}
        </div>
    </div>
@endsection