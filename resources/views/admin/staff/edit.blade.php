@extends('layouts.admin.admin')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Sửa tài khoản nhân viên</h2>

        <form action="{{ route('staffs.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Tên -->
            <div class="mb-3">
                <label class="form-label">Họ tên</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $staff->name) }}" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}" required>
            </div>

            <!-- Số điện thoại -->
            <div class="mb-3">
                <label class="form-label">Điện thoại</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}">
            </div>

            <!-- Ảnh đại diện -->
            <div class="mb-3">
                <label class="form-label">Ảnh đại diện</label>
                @if($staff->avatar)
                    <div class="mb-2">
                        <img src="{{ asset($staff->avatar) }}" width="120" class="rounded shadow">
                    </div>
                @endif
                <input type="file" name="avatar" class="form-control">
            </div>

            <!-- Giới tính -->
            <div class="mb-3">
                <label class="form-label">Giới tính</label>
                <select name="sex" class="form-select">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="male" @selected($staff->sex === 'male')>Nam</option>
                    <option value="female" @selected($staff->sex === 'female')>Nữ</option>
                    <option value="other" @selected($staff->sex === 'other')>Khác</option>
                </select>
            </div>

            <!-- Vai trò -->
            <div class="mb-3">
                <label class="form-label">Vai trò</label>
                <select name="role_id" class="form-select">
                    <option value="">-- Chọn vai trò --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected($staff->role_id == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Trạng thái -->
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="1" @selected($staff->status == 1)>Hoạt động</option>
                    <option value="0" @selected($staff->status == 0)>Ngừng hoạt động</option>
                </select>
            </div>

            <!-- Nút -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Cập nhật</button>
                <a href="{{ route('staffs.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection