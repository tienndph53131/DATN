@extends('layouts.admin.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Sửa tài khoản</h2>

    <form action="{{ route('accounts.update', $account->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Tên -->
        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $account->email) }}" required>
        </div>

        <!-- Số điện thoại -->
        <div class="mb-3">
            <label class="form-label">Điện thoại</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $account->phone) }}">
        </div>

        <!-- Ảnh đại diện -->
        <div class="mb-3">
            <label class="form-label">Ảnh đại diện</label>
            @if($account->avatar)
                <div class="mb-2">
                    <img src="{{ asset($account->avatar) }}" width="120" class="rounded shadow">
                </div>
            @endif
            <input type="file" name="avatar" class="form-control">
        </div>

                <!-- Giới tính -->
        <div class="mb-3">
            <label class="form-label">Giới tính</label>
            <select name="sex" class="form-select">
                <option value="">-- Chọn giới tính --</option>
                <option value="male" @selected($account->sex === 'male')>Nam</option>
                <option value="female" @selected($account->sex === 'female')>Nữ</option>
                <option value="other" @selected($account->sex === 'other')>Khác</option>
            </select>
        </div>

        <!-- Vai trò -->
        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="role_id" class="form-select">
                <option value="">-- Chọn vai trò --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected($account->role_id == $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Trạng thái -->
        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" @selected($account->status == 1)>Hoạt động</option>
                <option value="0" @selected($account->status == 0)>Ngừng hoạt động</option>
            </select>
        </div>

        <!-- Nút -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection
